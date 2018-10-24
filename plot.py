#!/usr/bin/env python36
import _mysql
import matplotlib as mpl
import matplotlib.dates as dates
mpl.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.ticker as ticker
import datetime

db=_mysql.connect("localhost", "warmkram", "warmkram", "warmkram")
db.query("""SELECT * FROM templog WHERE timestamp > (UNIX_TIMESTAMP() - 60*60*24*7) AND temp > -20;""")
r=db.store_result()
data = r.fetch_row(0)

db.query("""SELECT * FROM config LIMIT 1;""")
r = db.store_result()
config = r.fetch_row(0)
targetTemperature = config[0][0]
targetHumidity = config[0][1]


timestamp = [datetime.datetime.utcfromtimestamp(int(time)) for (time, _ , _ , _) in data]
temp = [float(temp) for (_ ,temp , _ , _) in data]
humid = [float(humid) for (_ , _ , humid , _) in data]
#heater = [time for (time, _, _, status ) in data]

fig = plt.figure()
axT = plt.subplot(111)

axT.plot(timestamp, temp, label="Temperature", color='r')
plt.ylabel("Temperature in °C")
axT.yaxis.set_major_locator(ticker.AutoLocator())
axT.yaxis.set_minor_locator(ticker.AutoMinorLocator())


axH = axT.twinx()
axH.plot(timestamp, humid, label="Humidity", color='b')
axH.set_ylim(0, 100)
plt.ylabel("Humidity in rel. %")
axH.yaxis.set_major_locator(ticker.AutoLocator())
#axH.yaxis.set_minor_locator(ticker.AutoMinorLocator())

axT.xaxis.set_minor_locator(dates.HourLocator(byhour=[x*6 for x in range(1,int(24/6))]))
axT.xaxis.set_minor_formatter(dates.DateFormatter('%H:%M'))  # hours and minutes
axT.xaxis.set_major_locator(dates.DayLocator(interval=1))    # every day
axT.xaxis.set_major_formatter(dates.DateFormatter('\n%d.%m.%Y')) 
axT.set_xlim(left=timestamp[0])
axT.grid(which='major')

#axT.axhline(y=targetTemperature, label="Target", linestyle='dotted', color='r', alpha=0.5)
#plt.hlines(y=targetHumidity, xmin=axH.get_xlim()[0], xmax=axH.get_xlim()[1], label='Target', linestyle='dotted', color='b', alpha=0.5)


fig.legend()
fig.set_size_inches(14, 6)
plt.subplots_adjust(left=0.05, right=0.95, top=0.86, bottom=0.1)
fig.savefig('temp.png', dpi=70)

print("OK")
