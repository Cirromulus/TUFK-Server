#!/usr/bin/env python36
import _mysql
import matplotlib as mpl
import matplotlib.dates as dates
from matplotlib.ticker import MaxNLocator
mpl.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.ticker as ticker
import datetime
#from datetime import time, tzinfo, timedelta
import sys
import numpy
import math

def weighted_mean(x, N):
    if N % 2 == 1:
        N = N + 1
    weights = list(range(1,int(N/2),1))
    weights.extend(range(int(N/2),0,-1))
    #print(weights)
    weights = [w / sum(weights) for w in weights]
    #print(weights)
    avg = []
    xlen = len(x)
    for i in range(len(x)):
        #print(str(i) + str(x[i]))
        elem = 0
        for j in range(len(weights)):
            idx = i + (j - int(len(weights)/2))
            idx = 0 if idx < 0 else idx
            idx = i if idx >= xlen else idx
            #print("j, idx: " + str(j) + " " + str(idx))
            elem = elem + x[idx] * weights[j]
        avg.append(elem)
    #print("AVG: " + str(avg))
    return avg

days = 7
if(len(sys.argv) == 2):
    days = int(sys.argv[1])
    print(str(days) + " days of history")

db=_mysql.connect("localhost", "warmkram", "warmkram", "warmkram")
db.query("SELECT * FROM templog WHERE timestamp > (UNIX_TIMESTAMP() - 60*60*24*" + str(days) + ") AND temp > 5;")
r=db.store_result()
data = r.fetch_row(0)

db.query("""SELECT * FROM config LIMIT 1;""")
r = db.store_result()
config = r.fetch_row(0)[0]
ind = 0
targetTemperature = float(config[ind])
ind += 1
temp_max_delta = targetTemperature + float(config[ind])
ind += 1
temp_upper_limit = targetTemperature + float(config[ind])
ind += 1
temp_lower_limit = targetTemperature - float(config[ind])
ind += 1
targetHumidity = float(config[ind])
ind += 1
humid_lower_limit = targetHumidity - float(config[ind])
ind += 1
humid_upper_limit = targetHumidity + float(config[ind])

timestamp = [datetime.datetime.utcfromtimestamp(int(time)) for (time, _ , _ , _) in data]
temp = [float(temp) for (_ ,temp , _ , _) in data]
humid = [float(humid) for (_ , _ , humid , _) in data]

avg_windowsize = math.floor(len(timestamp) / (days * 4))

avg_temp  = weighted_mean(temp , avg_windowsize)
avg_humid = weighted_mean(humid, avg_windowsize)

print(str(len(timestamp)) + " samples")

fire   = ([],[])
motion = ([],[])
heater = ([],[])
vent   = ([],[])
for (timeS , _ , _ , statS) in data:
    #firesens | motionsens | heater | vent
    time = datetime.datetime.utcfromtimestamp(int(timeS))
    stat = int(statS)
    if stat & 1:
        vent[0].append(time)
        vent[1].append(10)
    
    if stat & (1 << 1):
        heater[0].append(time)
        heater[1].append(15)
    
    if stat & (1 << 2):
        motion[0].append(time)
        motion[1].append(20)

    if stat & (1 << 3):
        fire[0].append(time)
        fire[1].append(25)
    
fig = plt.figure()
axT = plt.subplot(111)

axT.plot(timestamp, temp, label="Temperature", linewidth=1.25, color='r', alpha=1)
plt.ylabel("Temperature in °C")
axT.yaxis.set_major_locator(ticker.AutoLocator())
axT.yaxis.set_minor_locator(ticker.AutoMinorLocator())
#axT.set_ylim(bottom=0)

axH = axT.twinx()
axH.plot(timestamp, humid, label="Humidity", linewidth=1.25, color='b', alpha=0.8)
axH.set_ylim(0, 100)
plt.ylabel("Humidity in rel. %")
axH.yaxis.set_major_locator(ticker.AutoLocator())
axH.yaxis.set_minor_locator(ticker.AutoMinorLocator())

#axT.plot(timestamp[math.floor(avg_windowsize/2)-1:math.floor(-avg_windowsize/2)], avg_temp , color='#FF7000', alpha=0.8)
#axH.plot(timestamp[math.floor(avg_windowsize/2)-1:math.floor(-avg_windowsize/2)], avg_humid, color='#00A0FF', alpha=0.8)
axT.plot(timestamp, avg_temp , color='#ffa393', alpha=0.8)
axH.plot(timestamp, avg_humid, color='#1090FF', alpha=0.8)

axT.xaxis.set_minor_locator(dates.HourLocator(byhour=[x*6 for x in range(1,int(24/6))]))
axT.xaxis.set_minor_formatter(dates.DateFormatter('%H:%M'))  # hours and minutes
axT.xaxis.set_major_locator(dates.DayLocator(interval=1))    # every day
axT.xaxis.set_major_formatter(dates.DateFormatter('\n%d.%m.%Y')) 
axT.set_xlim(left=timestamp[0])
axT.set_ylim(bottom=0)
axT.grid(which='major')
axT.yaxis.set_major_locator(MaxNLocator(integer=True))

axT.axhline(targetTemperature, label="Min. Temp.", linestyle='--', color='r', alpha=0.5)
axT.axhline(temp_max_delta,                        linestyle=':', color='r', alpha=0.15)
axT.axhline(temp_upper_limit,                      linestyle='-.', color='r', alpha=0.25)
if(temp_lower_limit != targetTemperature):
    axT.axhline(temp_lower_limit,                      linestyle='-.', color='r', alpha=0.25)


axH.axhline(targetHumidity, label="Max. Humid.", linestyle='--', color='b', alpha=0.5)
if(humid_upper_limit != targetHumidity):
    axH.axhline(humid_upper_limit,                   linestyle='-.', color='b', alpha=0.4)
axH.axhline(humid_lower_limit,                   linestyle='-.', color='b', alpha=0.4)

axH.scatter(vent[0], vent[1], label='Vent', color='g', marker='2')
axH.scatter(heater[0], heater[1], label='Heater', color='y', marker='o')
axH.scatter(motion[0], motion[1], label='Motion', color='b', marker='^')
axH.scatter(fire[0], fire[1], label='Fire', color='r', marker='*')

fig.legend(ncol=2)
fig.set_size_inches(15, 6)
plt.subplots_adjust(left=0.05, right=0.95, top=0.86, bottom=0.1)
fig.savefig('temp.png', dpi=150)

print("OK")
