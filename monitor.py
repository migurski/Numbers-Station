#!/usr/bin/env python

from os import environ
from subprocess import Popen, PIPE

from MySQLdb import connect
from boto import connect_cloudwatch

if __name__ == '__main__':

    cw = connect_cloudwatch(environ['AWS_KEY'], environ['AWS_SECRET'])
    db = connect(host=environ['DB_HOST'], user=environ['DB_USER'], passwd=environ['DB_PASS'], db=environ['DB_NAME']).cursor()
    
    db.execute('BEGIN')
    db.execute('SELECT numbers_made, seconds_spent, fastest_number, slowest_number FROM monitor LIMIT 1')

    numbers, seconds, fastest, slowest = db.fetchone()
    stats = dict(maximum=slowest, minimum=fastest, samplecount=numbers, sum=seconds)
    
    if numbers > 0:

        cw.put_metric_data('Mission Integers', 'Turnaround', statistics=stats, unit='Seconds')
        cw.put_metric_data('Mission Integers', 'Numbers', value=numbers, unit='Count')
        
        db.execute('UPDATE monitor SET numbers_made=0, seconds_spent=0, fastest_number=9999, slowest_number=0')
    
    db.execute('COMMIT')
    db.close()
