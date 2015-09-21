from instagram.client import InstagramAPI
import json
import ftplib
import datetime
import numpy as np


# ===============================================
def connect_to_server(): 
    
    cinfo = None
    
    print('Connecting to ftp server..')  
        
    # connecting to server    
    try:
        cinfo = ftplib.FTP("cafe57-ru.1gb.ru")
        cinfo.login("w_cafe57-ru_9cf0f4f5", "2abzzed2xv")                
    except:
        print('Connection error') 
    # go to folder
    try:
        cinfo.cwd("/http/json_data")
    except:
        print('Could not find json_data folder')        
    
    if cinfo:
        print('Connection successful')      
    else:
        print('BAD ИНТЕРНЕТ')  
        
    
    return cinfo
# ===============================================
def break_connection(cinfo):
    # break connection  
    print('Closing connections..')   
    
    cinfo.quit()  
    
    print('Connection closed') 
    
    return None
# ===============================================
def download_file(cinfo, filename, type):
    # loading database
    
#    filename = 'db_ig.json'
    localfile = open(filename, 'wb')
    
    cinfo.retrbinary('RETR %s' % filename, localfile.write)
    
    localfile.close()
    
    localfile = open(filename, 'r')
    s = localfile.read()
    if type == 'json':        
        try:
            val = json.loads(s)
        except:
            # TODO
            print('File', filename, 'is broken. Replacing with empty json')
            print('Consider replacing other files')
            val = {}
    else:
        if type == 'list':
            val = s.split()
        else: 
            if type == 'int':
                val = int(s)
        
    localfile.close()
    
    return val
# ===============================================    
def connect_to_ig_api():
    # connecting to IG API
    client_id = 'd35835c288144f1fa556821193047e0e'
    client_secret = 'f84a75aac6ec499ea786fe016b19fa62'
    access_token = '555234110.d35835c.5b14130da07945d3aa391455b827af47'
    client_ip = '1.2.3.4'
    
    api = InstagramAPI(client_id=client_id, client_secret=client_secret,
                       client_ips= client_ip,access_token= access_token)
                       
    return api
# ===============================================
def check_db_likes(db, ig_api):
    # for every media_id in database:
    # ---- check if this photo still exists
    # ---- if yes:
    # -------- check if likes in database != likes in IG
    # -------------if yes, change value in database
    # ---- if no:
    # -------- delete record from database
    to_pop = []
    for media_id in db.keys():   
#        print('Checking likes for media_id', media_id)
        print('    ', end="")
        record = db[media_id]
        try:
            likes = ig_api.media_likes(media_id)
#            print('Likes loaded')
            print(':', end="")
            likes = len(likes)

            if likes != record[1]:
                record[1] = likes
                db[media_id] = record
#                print('Likes changed')
                print('O', end="")
            else:
#                print('Likes same')
                print('|', end="")
        except:
#            print('Photo deteted')
            print(':X', end="")
            to_pop.append(media_id)
    
    print('To remove:', len(to_pop), 'photos', end = '\n')     
    for popper in to_pop:
        db.pop(popper)
        
    return db   
# ===============================================
def check_db_stopwords(db, stop_list):
    
    to_pop = []
     
    for media_id in db.keys():   
#        print('Checking stop words for media_id', media_id)
        print('   :', end="")
        record = db[media_id]

        stop = False        
        
        for word in stop_list:
            if record[0].find(word) + 1:
#                print('Stop word found!')
                stop = True
                break
            
        if stop:
            print('$', end="")
            to_pop.append(media_id)
        else:
            print(')', end="")
    
    print('To remove:', len(to_pop), 'photos', end = '\n')     
    for popper in to_pop:
        db.pop(popper)
            
    return db   
# ===============================================
def load_media_list_by_tag(tag, ig_api, tagtime):
    print('Starting load media with #', tag)
    
    max_cnt = 10000
    cnt = 0
    timeflag = True
    
    lst = []
    mti = None
    media_ids, nexts = ig_api.tag_recent_media(tag_name = tag, count = 50, max_tag_id = mti)
    for media_id in media_ids:
        if media_id.created_time < tagtime:
             timeflag = False
             print('Too far in the past! Go home, Marty!')
             break
        lst.append(media_id.id)
        
             
    cnt += len(lst)
    if nexts:    
        mti = int(nexts.split('max_tag_id=')[1])
    
    while nexts and cnt < max_cnt and timeflag:
        print('Requesting..')
        media_ids, nexts = ig_api.tag_recent_media(tag_name = tag, count = 50, max_tag_id = mti)
        if nexts:
            mti = int(nexts.split('max_tag_id=')[1])
            
            print('MAX_TAG answer', mti)
            print('Parsing answer..')
            for media_id in media_ids:
                if media_id.created_time < tagtime:
                    timeflag = False
                    print('Too far in the past! Go home, Marty!')
                    break
                lst.append(media_id.id)
                    
            cnt += len(lst)
            print('Total:', len(lst))      
       
    return lst
# ===============================================
def upload_file(cinfo, filename, data):
        # loading database
    with open(filename, 'w') as f:
        json.dump(data, f)

    cinfo.storbinary('STOR %s' % filename, open(filename, 'rb')) 
# ===============================================
def upload_to_server(cinfo, db, weeklist, monthlist, alllist, tagtimes):
    
    if len(weeklist) > 0:
        upload_file(cinfo, 'ig_top_week.json', weeklist)
    
    if len(monthlist) > 0:    
        upload_file(cinfo, 'ig_top_month.json', monthlist)
        
    upload_file(cinfo, 'ig_top_all.json', alllist)
    
    upload_file(cinfo, 'ig_db.json', db)
    
    upload_file(cinfo, 'ig_tagtimes.json', tagtimes)
#    with open(filename, 'w') as f:
#        json.dump(data, f)

#    cinfo.storbinary('STOR %s' % filename, open(filename, 'rb')) 

# ===============================================
def mediaid_to_list(media_id, ig_api):
    media = ig_api.media(media_id = media_id)
    if media:
        lst = []
#        lst.append(media_id)
        if media.caption:
            lst.append(media.caption.text)
        else:
            lst.append("")
            
        lst.append(media.like_count)
        lst.append(media.comment_count)
        lst.append(media.user.username)
        lst.append(media.created_time.strftime(format = '%x %X'))
        lst.append(media.images['low_resolution'].url)
#        infolist.append(lst)
#        likeslist.append(media.like_count)

    return lst
# ===============================================    
def make_wm_db(db):
    db_week = {}
    db_month = {}
    now = datetime.datetime.now()
    
    dw = datetime.timedelta(days = 7)
    dm = datetime.timedelta(days = 30)
    for key in db.keys():
        record = db[key]
        recordtime = datetime.datetime.strptime(record[4], '%m/%d/%y %H:%M:%S')
#        print(recordtime + dm, now, recordtime - dw >= now)
#        print(now)
        if recordtime + dw >= now:
            db_week.update({key: record})
        if recordtime + dm >= now:
            db_month.update({key: record})    
            
    return db_week, db_month
# ===============================================      
def top20_json_list(db):
    lst_likes = []
    lst_all = []

    for key in db.keys():
        record = db[key]
        lst_all.append(record)
        lst_likes.append(record[1])
    
    idxx = sorted(range(len(lst_likes)), key = lst_likes.__getitem__)
    idxx = idxx[::-1]
    
    n = min(len(idxx), 20)
    json_list = []
    for cnt in range(n):
#        print(cnt)11
        lst = lst_all[idxx[cnt]]
        
        json_list.append({'date':lst[4], 'url320':lst[5], 'text':lst[0], \
            'source':lst[3], 'likes':lst[1], 'numcomm':lst[2]})    
       
    return json_list
# =============================================== 
def process_media_list(lst, media_id_dict, db, stop_list):
    cnt = 0;
    n = len(lst)
    for media_id in lst:
        cnt = cnt + 1
#        print('For tag #', tag, 'processing media id', media_id)
        print('#', tag, ':', cnt, 'of', n)
        try: 
            media_id_dict[media_id]
#            print('Already in media_dict!')
            print(':/')
            continue
        except:
            None            
            
        try: 
            db[media_id]
            # already in dict
#            print('Already in database!')
            print('://')
            continue
        except:
            None 
            
        infolist = mediaid_to_list(media_id, ig_api)
        
        is_ok = True
        
        media_text = infolist[0]
        for word in stop_list:
            if media_text.find(word) + 1:
#                print('Stop word found!')
                print(':$')
                is_ok = False
                break
                
        if is_ok:
            media_id_dict.update({media_id: infolist})
        
    return media_id_dict
# =============================================== 
def download_from_server(cinfo):
    print('Downloading database from server')
    db = download_file(cinfo, 'ig_db.json', 'json')
    if type(db) == list:
        print('Database is empty or broken')
        db = dict()
    
    print('Downloading tag list from server')
    tags_list = download_file(cinfo, 'ig_tags_list.txt', 'list')
    print('Downloading stop-list from server')
    stop_list = download_file(cinfo, 'ig_stop_list.txt', 'list')
    print('Downloading tag timing from server')
    tagtimes =  download_file(cinfo, 'ig_tagtimes.json', 'json')
    if type(tagtimes) == list:
        print('Tag times are empty or broken')
        tagtimes = dict()
    
    return db, tags_list, stop_list, tagtimes
# =============================================== 
# =============================================== 
cinfo = connect_to_server()
db, tags_list, stop_list, tagtimes = download_from_server(cinfo)
break_connection(cinfo)

ig_api = connect_to_ig_api()
pics = list(db.keys())
users = set()

for value in db.values():
    users.add(value[3])

users = list(users)
usersdict = dict((users[k],k) for k in range(len(users)))
picsdict = dict((pics[k],k) for k in range(len(pics)))

other_users = set()

m = np.zeros((len(pics), len(users)), dtype = bool)
cntt = 0
for media_id in db.keys():
    cntt += 1
    print(cntt, 'of', len(pics))    
    try:
        temp = ig_api.media_likes(media_id)
    except:
        print('FAYOL!')
    media_id_num = picsdict[media_id]
    m[media_id_num, usersdict[db[media_id][3]]] = True
    for user in temp:
        try:
            m[media_id_num, usersdict[user.username]] = True
        except:
#            print(':3 ', user.username)
            other_users.add(user.username)


import pickle
pickle.dump( m, open( "save.p", "wb" ), protocol = 2 )
m = pickle.load( open( "save.p", "rb" ) )

import numpy as np
from matplotlib import pyplot as plt
plt.matshow(m, cmap=plt.cm.Blues)

from sklearn.cluster.bicluster import SpectralBiclustering
from sklearn.metrics import consensus_score
model = SpectralBiclustering(method='bistochastic', n_jobs = -1)
model.fit(m)

fit_data = m[np.argsort(model.row_labels_)]
fit_data = fit_data[:, np.argsort(model.column_labels_)]

plt.matshow(fit_data, cmap=plt.cm.Blues)
plt.title("After biclustering; rearranged to show biclusters")
