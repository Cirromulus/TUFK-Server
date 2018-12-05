#!/usr/bin/env python36
import _mysql
import matplotlib as mpl
import matplotlib.dates as dates
mpl.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.ticker as ticker
import datetime
import sys

days = 7
if(len(sys.argv) == 2):
    days = int(sys.argv[1])

db=_mysql.connect("localhost", "warmkram", "warmkram", "warmkram")
db.query("SELECT * FROM templog WHERE timestamp > (UNIX_TIMESTAMP() - 60*60*24*" + str(days) + ") AND temp > 5;")
r=db.store_result()
data = r.fetch_row(0)

db.query("""SELECT * FROM config LIMIT 1;""")
r = db.store_result()
config = r.fetch_row(0)
targetTemperature = float(config[0][0])
targetHumidity = float(config[0][1])


timestamp = [datetime.datetime.utcfromtimestamp(int(time)) for (time, _ , _ , _) in data]
temp = [float(temp) for (_ ,temp , _ , _) in data]
humid = [float(humid) for (_ , _ , humid , _) in data]

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

axT.plot(timestamp, temp, label="Temperature", color='r')
plt.ylabel("Temperature in °C")
axT.yaxis.set_major_locator(ticker.AutoLocator())
axT.yaxis.set_minor_locator(ticker.AutoMinorLocator())
#axT.set_ylim(bottom=0)

axH = axT.twinx()
axH.plot(timestamp, humid, label="Humidity", color='b')
axH.set_ylim(0, 100)
plt.ylabel("Humidity in rel. %")
axH.yaxis.set_major_locator(ticker.AutoLocator())
axH.yaxis.set_minor_locator(ticker.AutoMinorLocator())

axT.xaxis.set_minor_locator(dates.HourLocator(byhour=[x*6 for x in range(1,int(24/6))]))
axT.xaxis.set_minor_formatter(dates.DateFormatter('%H:%M'))  # hours and minutes
axT.xaxis.set_major_locator(dates.DayLocator(interval=1))    # every day
axT.xaxis.set_major_formatter(dates.DateFormatter('\n%d.%m.%Y')) 
axT.set_xlim(left=timestamp[0])
axT.set_ylim(bottom=0)
axT.grid(which='major')

axT.axhline(targetTemperature, label="Min. Temp.", linestyle='dotted', color='r', alpha=0.5)
axH.axhline(targetHumidity, label="Max. Humid.", linestyle='dotted', color='b', alpha=0.5)

axH.scatter(vent[0], vent[1], label='Vent', color='g', marker='2')
axH.scatter(heater[0], heater[1], label='Heater', color='y', marker='o')
axH.scatter(motion[0], motion[1], label='Motion', color='b', marker='^')
axH.scatter(fire[0], fire[1], label='Fire', color='r', marker='*')

fig.legend(ncol=2)
fig.set_size_inches(14, 6)
plt.subplots_adjust(left=0.05, right=0.95, top=0.86, bottom=0.1)
fig.savefig('temp.png', dpi=70)

print("OK")
