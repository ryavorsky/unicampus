import urllib.request
import os
import os.path
import time

uids_file_name = "uids.txt"

# Get individual statistics for the given UID
def get_stat(uid, dir_name):

    print ("stat", uid, dir_name)
    res_file_name = os.path.join(dir_name, "stat", uid + ".json")
    res_file = open(res_file_name, 'w')
    
    url_stat = "https://api.vk.com/method/users.get?uids=" + uid + "&fields=domain,counters,city,country,photo_max"
    print(url_stat)
    with urllib.request.urlopen(url_stat) as url:
        stat = url.read().decode('utf-8')
        
    res_file.write(stat)
    res_file.close()


# Get list of friends for a given UID
def get_friends(uid, dir_name):
    
    print ("friends", uid, dir_name)

    res_file_name = os.path.join(dir_name, "friends", uid + ".json")
    res_file = open(res_file_name, 'w')
    
    url_friends = "https://api.vk.com/method/friends.get?user_id=" + uid
    with urllib.request.urlopen(url_friends) as url:
        friends = url.read().decode('utf-8')
        
    res_file.write(friends)
    res_file.close()

    
def main():

    with open(uids_file_name) as f:
        uids = [x.strip() for x in f.readlines()]
        
    dir_name = time.strftime("%Y%b%d_%H%M_%S")
    os.mkdir(dir_name)
    os.mkdir(os.path.join(dir_name, "friends"))
    os.mkdir(os.path.join(dir_name, "stat"))

    for uid in uids :
        get_stat(uid, dir_name)
        get_friends(uid, dir_name)        

main()
