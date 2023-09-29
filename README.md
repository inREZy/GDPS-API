# GDPS-API
A small addon to the core of a private server written in php, which works through an API key and a POST method. With it, you can get data about the user, level, etc.
## How to setup
- Edit `$key` variable in `config/api.php`.
- Drag & drop GDPS-API files to your server.
- You're all done!
## How to use
- Like in python you can get data from api
```python
import requests

data = {
    "key": "your_key_here",
    "id": 2
}

res = requests.post("http://example.com/database/api/levelInfo.php", data).json()

match res["success"]:
    case True:
        print(res["level"]["name"])
    case _:
        print(f"[ERROR] {res['message']}")
```
## POST Data
All files requires `key`
- levelInfo.php `id`
- levelTimely.php `type` (0 or 1)
- songAdd.php `link`
- songLatest.php
- songList.php `page`
- songSearch.php `query`
- userLevelSearch.php `user` `page`
- userStats.php `user`
## Important
Tested on php 7.1
