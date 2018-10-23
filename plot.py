#!/usr/bin/env python36
import _mysql
import matplotlib as mpl
import matplotlib.dates as dates
mpl.use('Agg')
import matplotlib.pyplot as plt
import datetime

db=_mysql.connect("localhost", "warmkram", "warmkram", "warmkram")
db.query("""SELECT * FROM templog WHERE timestamp > (UNIX_TIMESTAMP() - 60*60*24*7) AND temp > -20;""")
r=db.store_result()
data = r.fetch_row(0)

timestamp = [datetime.datetime.utcfromtimestamp(int(time)) for (time, _ , _ , _) in data]
temp = [float(temp)for (_ ,temp , _ , _) in data]
humid = [float(humid)for (_ , _ , humid , _) in data]

fig = plt.figure()
axT = fig.add_subplot(111)

axT.plot(timestamp, temp, label="Temperature", color='r')
plt.ylabel("Temperature in °C")

axH = axT.twinx()
axH.plot(timestamp, humid, label="Humidity", color='b')
axH.set_ylim(0, 100)
plt.ylabel("Humidity in rel. %")

axT.xaxis.set_minor_locator(dates.HourLocator(interval=5))   # every 4 hours
axT.xaxis.set_minor_formatter(dates.DateFormatter('%H:%M'))  # hours and minutes
axT.xaxis.set_major_locator(dates.DayLocator(interval=1))    # every day
axT.xaxis.set_major_formatter(dates.DateFormatter('\n%d.%m.%Y')) 
axT.grid(which='major')


fig.legend()
fig.set_size_inches(13, 6)
fig.savefig('temp.png', dpi=70)

print("OK")
