import requests, re, json, MySQLdb, time, urllib

def getartists(user, page, uid):
  db=MySQLdb.connect(host = "localhost", user = "vudb", passwd = "k1QFSTrIDs7TcwanJbzV", db = "vudb", charset='utf8')
  a=db.cursor()
  try:
    a.execute("""SELECT id  FROM """+str(uid)+"""_artists""")
  except:
    a.execute("""CREATE TABLE """+str(uid)+"""_artists (id INTEGER PRIMARY KEY AUTO_INCREMENT, aid INTEGER(6), playcount INTEGER(5), playtime INTEGER(11), time TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP)""")
    db.commit()
  methode="method=user.getTopArtists&user="+user+"&page="+str(page);
  api_key="830d6e2d4d737d56aa1f94f717a477df"
  r = requests.get('https://ws.audioscrobbler.com/2.0/?format=json&api_key='+api_key+'&'+methode)
  data = json.loads(r.text)
  pages=int(data['topartists']['@attr']['totalPages'])
  for artist  in data['topartists']['artist']:
    name=artist['name']
    mbid=artist['mbid']
    count=artist['playcount']
    d=db.cursor()
    d.execute("""SELECT id  FROM lastfm_artists WHERE mbid =%s""", [mbid])
    res=d.fetchone()
    if not res:
      d.execute( """INSERT INTO lastfm_artists (name, mbid) VALUES (%s, %s)""", [name, mbid])
      db.commit()
    d=db.cursor()
    d.execute("""SELECT id  FROM lastfm_artists WHERE mbid =%s""", [mbid])
    res=d.fetchone()
    aid=res[0]
    c=db.cursor()
    c.execute("""SELECT id  FROM """+str(uid)+"""_artists WHERE aid =%s""", [aid])
    res=c.fetchone()
    if not res:
      c.execute( """INSERT INTO """+str(uid)+"""_artists (aid, playcount, playtime) VALUES (%s, %s, 0)""", [aid, count])
      db.commit()
  #if res and res[0]!="":
    #c.execute( """Update chat_uni SET stat = 2 WHERE chat_id=%s""", [update.message.chat_id])
    #db.commit()
  db.close()
  return pages

def artist_user():
  db=MySQLdb.connect(host = "localhost", user = "vudb", passwd = "k1QFSTrIDs7TcwanJbzV", db = "vudb")
  c=db.cursor()
  c.execute("""SELECT username, id  FROM ldkf_lastfm""")
  data= c.fetchall()
  for userinfo  in data:
    user=userinfo[0]
    uid=userinfo[1]
    page=1
    pages=getartists(user, page, uid)
    while page<pages:
      page=page+1
      getartists(user,page, uid)
  db.close()
  
#############################################################################################

def topalbum(mbida, art_name, aid, page, db):
  if mbida != "":
    methode="method=artist.getTopAlbums&mbid="+mbida+"&limit=50"+"&page="+str(page)
  else:
    methode="method=artist.getTopAlbums&artist="+art_name+"&limit=50"+"&page="+str(page)
  api_key="830d6e2d4d737d56aa1f94f717a477df"
  r = requests.get('https://ws.audioscrobbler.com/2.0/?format=json&api_key='+api_key+'&'+methode)
  data = json.loads(r.text)
  pages=int(data['topalbums']['@attr']['totalPages'])
  for album  in data['topalbums']['album']:
    name=album['name']
    try:
      mbid=album['mbid']
    except:
     #ripinpeace
      mbid=""
    d=db.cursor()
    if re.match(u"[^\u0000-\uffff]", name):
      print("Kein UTF8")
    else:
      d.execute("""SELECT id FROM lastfm_album WHERE name =%s and aid=%s""", [name, aid])
      res=d.fetchone()
      if name != "(null)":
        if not res:
          d.execute( """INSERT INTO lastfm_album (aid, name, mbid) VALUES (%s, %s, %s)""", [aid, name, mbid])
          db.commit()
        c=db.cursor()
        c.execute("""SELECT username, id  FROM ldkf_lastfm""")
        data= c.fetchall()
        for userinfo  in data:
          user=userinfo[0]
          uid=userinfo[1]
          a=db.cursor()
          try:
            a.execute("""SELECT id  FROM """+str(uid)+"""_album""")
            res=a.fetchone()
          except:
            a.execute("""CREATE TABLE """+str(uid)+"""_album (id INTEGER PRIMARY KEY AUTO_INCREMENT, alid INTEGER(8), aid INTEGER(6), playcount INTEGER(5), playtime INTEGER(11), time TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP)""")
            db.commit() #tabelle anlegen, wenn für album nicht existent
          d=db.cursor()
          d.execute("""SELECT id  FROM lastfm_album WHERE name =%s and aid=%s""", [name, aid])
          res=d.fetchone()
          alid=res[0]
          b=db.cursor()
          b.execute("""SELECT id  FROM """+str(uid)+"""_artists WHERE aid=%s""", [aid]) #id vom user_artist, wo die artist id dieses albums steht (wenn es da steht)
          rest=b.fetchone()
          #print(res[0])
          if rest and rest[0]!="":
            a=db.cursor()
            a.execute("""SELECT id  FROM """+str(uid)+"""_album WHERE alid=%s""", [alid])
            resa=a.fetchone()
            if not resa:
              c=db.cursor()
              c.execute( """INSERT INTO """+str(uid)+"""_album (alid, aid, playcount, playtime) VALUES (%s, %s, 0, 0)""", [alid, aid])
              db.commit() #album in user_db eintragen, wenn aid in der user_artist stand (diese dann für getinfo, weil username
   # else:
    #  print("null")
  return pages

def artist_album():
  db=MySQLdb.connect(host = "localhost", user = "vudb", passwd = "k1QFSTrIDs7TcwanJbzV", db = "vudb", charset='utf8')
  c=db.cursor()
  c.execute("""SELECT mbid, name, id FROM lastfm_artists WHERE id >2000""")
  data= c.fetchall()
  for artist  in data:
    mbida=artist[0]
    art_name=artist[1]
    aid=artist[2]
    page=1
    print(aid)
    pages=topalbum(mbida, art_name, aid, page, db)
   
  db.close()

def album():
  db=MySQLdb.connect(host = "localhost", user = "vudb", passwd = "k1QFSTrIDs7TcwanJbzV", db = "vudb", charset='utf8')
  c=db.cursor()
  c.execute("""SELECT username, id  FROM ldkf_lastfm WHERE id>7""")
  data= c.fetchall()
  countofuser=len(data)
  for userinfo  in data:
    user=userinfo[0]
    uid=userinfo[1]
    d=db.cursor()
    d.execute("""SELECT alid, aid FROM """+str(uid)+"""_album WHERE id>-1""") ####erst von user, dann album
    data= d.fetchall()
    for album  in data:
      alid=album[0]
      aid=album[1]
      al=db.cursor()
      al.execute("""SELECT name, mbid FROM lastfm_album WHERE id =%s""", [alid])
      res=al.fetchone()
      name=res[0]
      try:
        mbid=res[1]
      except:
        mbid=""
      d=db.cursor()
      if mbid=="":
        d.execute("""SELECT name  FROM lastfm_artists WHERE id =%s""", [aid])
        res=d.fetchone()
        artist_name=res[0]
        print(artist_name+": "+name)
        methode="method=album.getInfo&user="+user+"&artist="+artist_name+"&album="+urllib.parse.quote_plus(name)+"&autocorrect=1";
      else:
         methode="method=album.getInfo&user="+user+"&mbid="+mbid;
      api_key="830d6e2d4d737d56aa1f94f717a477df"
      r = requests.get('https://ws.audioscrobbler.com/2.0/?format=json&api_key='+api_key+'&'+methode)
      data = json.loads(r.text)
      t=0
      try:
        album =data['album']
        t=1
      except:
        t=0
        print("Missing Album")
      if t==1:
        tracks=album['tracks']
        try:
          count=album['userplaycount']
        except:
          count=0
        #print(album['name']+" Playcount: "+str(count)+" ("+user+")")
        if int(count)<1:
          d=db.cursor()
         # d.execute("""DELETE FROM """+str(uid)+"""_album WHERE alid = %s""", [alid])
         # db.commit()
         # print("deleted "+name+" from "+user)
        else:
          c=db.cursor()
          c.execute( """Update """+str(uid)+"""_album SET playcount=%s WHERE alid=%s""", [int(count), alid])
          db.commit()
          for track in tracks['track']:
            rank=track['@attr']['rank']
            duration=track['duration']
            name=track['name']
           # print(name)
            d=db.cursor()
            d.execute("""SELECT id FROM lastfm_tracks WHERE name =%s and alid=%s and aid=%s""", [name, alid, aid]) 
            res=d.fetchone()
            if not res and int(count)!=0 and name!="[untitled]":
              d.execute( """INSERT INTO lastfm_tracks (aid, alid, name, mbid, duration, rank) VALUES (%s, %s, %s, 0, %s, %s)""", [aid, alid, name, duration, rank])
              db.commit()
            if int(count)>0 and name!="[untitled]":
              a=db.cursor()
              try:
                a.execute("""SELECT id  FROM """+str(uid)+"""_tracks""")
                res=a.fetchone()
              except:
                a.execute("""CREATE TABLE """+str(uid)+"""_tracks (id INTEGER PRIMARY KEY AUTO_INCREMENT, alid INTEGER(8), aid INTEGER(6), tid INTEGER(10), playcount INTEGER(8), playtime INTEGER(11), time TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP)""")
                db.commit() #tabelle anlegen, wenn für track nicht existent
              d=db.cursor()
              d.execute("""SELECT id  FROM lastfm_tracks WHERE name =%s and alid=%s and aid=%s""", [name, alid, aid])
              res=d.fetchone()
              tid=res[0]
              d=db.cursor()
              d.execute("""SELECT id FROM """+str(uid)+"""_tracks WHERE tid =%s""", [tid])
              res=d.fetchone()
              if not res and int(count)!=0:
                c=db.cursor()
                c.execute( """INSERT INTO """+str(uid)+"""_tracks (alid, aid, tid, playcount, playtime) VALUES (%s, %s, %s, 0, 0)""", [alid, aid, tid])
                db.commit() 
       # else:
              #print(name+": Track already in Database")
      #if killalbum==countofuser:
      #  print("################Album kill: "+str(alid))
      #  d=db.cursor()
      #  d.execute("""DELETE FROM lastfm_album WHERE id = %s""", [alid])
      #  db.commit()
     
  db.close()

def track():
  db=MySQLdb.connect(host = "localhost", user = "vudb", passwd = "k1QFSTrIDs7TcwanJbzV", db = "vudb", charset='utf8')
  c=db.cursor()
  c.execute("""SELECT username, id  FROM ldkf_lastfm WHERE id>-1""")
  data= c.fetchall()
  for userinfo  in data:
    user=userinfo[0]
    uid=userinfo[1]
    b=db.cursor()
    b.execute("""SELECT id, tid, aid  FROM """+str(uid)+"""_tracks""") #id vom user_artist, wo die artist id dieses albums steht (wenn es da steht)
    data= b.fetchall()
    for track  in data:
      pid=track[0]
      d=db.cursor()
      tid=track[1]
      aid=track[2]
      d=db.cursor()
      d.execute("""SELECT name, duration  FROM lastfm_tracks WHERE id =%s""", [tid])
      res=d.fetchone()
      trackname=res[0]
      duration=res[1]
      d=db.cursor()
      d.execute("""SELECT name  FROM lastfm_artists WHERE id =%s""", [aid])
      res=d.fetchone()
      artistname=res[0]
      methode="method=track.getInfo&track="+trackname+"&username="+user+"&artist="+artistname+"&autocorrect=1";
      api_key="830d6e2d4d737d56aa1f94f717a477df"
      r = requests.get('https://ws.audioscrobbler.com/2.0/?format=json&api_key='+api_key+'&'+methode)
      data = json.loads(r.text)
      try:
        track=data['track']
      except:
        print(trackname+" von "+artistname)
      try:
        playcount=track['userplaycount'] 
      except:
        playcount=0
      try:
        mbid=track['mbid']
        #print(mbid)
      except:
        mbid=""
      c=db.cursor()
      c.execute( """Update """+str(uid)+"""_tracks SET playcount = %s, playtime = %s, time= CURRENT_TIMESTAMP WHERE id=%s""", [playcount, int(duration)*int(playcount), pid])
      db.commit()
      c.execute( """Update lastfm_tracks SET mbid = %s WHERE id=%s""", [mbid, pid])
      db.commit()
  db.close()

def artist_playtime():
  db=MySQLdb.connect(host = "localhost", user = "vudb", passwd = "k1QFSTrIDs7TcwanJbzV", db = "vudb", charset='utf8')
  c=db.cursor()
  c.execute("""SELECT username, id  FROM ldkf_lastfm""")
  data= c.fetchall()
  for userinfo  in data:
    user=userinfo[0]
    print(user)
    uid=userinfo[1]
    b=db.cursor()
    b.execute("""SELECT id  FROM """+str(uid)+"""_artists""") 
    data= b.fetchall()
    for artists  in data:
      aid=artists[0]
      a=db.cursor()
      a.execute("""SELECT playcount, playtime  FROM """+str(uid)+"""_tracks WHERE aid=%s""", [aid])
      data= a.fetchall()
      playcount=0
      playtime=0
      for tracks  in data:
        playcount=playcount+tracks[0]
        playtime=playtime+tracks[1]
      c=db.cursor()
      c.execute( """Update """+str(uid)+"""_artists SET playcount = %s, playtime = %s WHERE id=%s""", [int(playcount), int(playtime), aid])
      db.commit()
  db.close()  
  
  
def album_playtime():
  db=MySQLdb.connect(host = "localhost", user = "vudb", passwd = "k1QFSTrIDs7TcwanJbzV", db = "vudb", charset='utf8')
  c=db.cursor()
  c.execute("""SELECT username, id  FROM ldkf_lastfm""")
  data= c.fetchall()
  for userinfo in data:
    user=userinfo[0]
    print(user)
    uid=userinfo[1]
    b=db.cursor()
    b.execute("""SELECT id  FROM """+str(uid)+"""_album""") 
    data= b.fetchall()
    for album  in data:
      alid=album[0]
      a=db.cursor()
      a.execute("""SELECT playcount, playtime  FROM """+str(uid)+"""_tracks WHERE alid=%s""", [alid])
      data= a.fetchall()
      playcount=0
      playtime=0
      for tracks  in data:
        playcount=playcount+tracks[0]
        playtime=playtime+tracks[1]
      ######check if track in db cause of deleting above#or just dont delete it
      c=db.cursor()
      c.execute( """Update """+str(uid)+"""_album SET playtime = %s WHERE id=%s""", [int(playtime), alid])
      db.commit()
  db.close()


print("Künstler")
#artist_user()
print("Alben der Künstler")
#artist_album() #alben der künstler einlesen
print("Info Alben")
album() #titel der alben und albeninfos auslesen#
print("Tracks")
#track() #trackdaten auslesen
print("Alben Playtime")
#album_playtime() #trackdaten auf album hochrechnen
print("Künstler Playtime")
#artist_playtime() #trackdaten auf künstler hochrechnen.
