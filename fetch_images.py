import sqlite3
import urllib.request
import urllib.parse
import json
import time
import os
import mysql.connector

# Connect to mysql
conn = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="",
    database="lar_demo"
)
cursor = conn.cursor(dictionary=True)
cursor.execute("SELECT id, name FROM products")
products = cursor.fetchall()

def get_image_url(query):
    # Using duckduckgo html search to find an image
    try:
        q = urllib.parse.quote(query + " lens white background")
        req = urllib.request.Request(
            f"https://html.duckduckgo.com/html/?q={q}",
            data=None,
            headers={
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
            }
        )
        with urllib.request.urlopen(req) as response:
            html = response.read().decode('utf-8')
            # very naive parsing for images
            # duckduckgo might not have images in html version easily accessible
    except Exception as e:
        print(f"Error {e}")
    return None

import re
import requests

def search_image(query):
    # Use Yahoo image search or similar
    url = f"https://images.search.yahoo.com/search/images?p={urllib.parse.quote(query + ' lens product')}"
    headers = {'User-Agent': 'Mozilla/5.0'}
    res = requests.get(url, headers=headers)
    matches = re.findall(r"imgurl=(http[^&]+)", res.text)
    if matches:
        return urllib.parse.unquote(matches[0])
    return None

os.makedirs('public/uploads/products', exist_ok=True)

for p in products:
    print(f"Processing {p['id']} - {p['name']}")
    img_url = search_image(p['name'])
    if img_url:
        print(f"Found: {img_url}")
        try:
            # Download image
            ext = img_url.split('.')[-1].split('?')[0]
            if len(ext) > 4: ext = 'jpg'
            filename = f"lens_{p['id']}.{ext}"
            filepath = f"public/uploads/products/{filename}"
            
            img_data = requests.get(img_url, headers={'User-Agent': 'Mozilla/5.0'}, timeout=10).content
            with open(filepath, 'wb') as f:
                f.write(img_data)
                
            db_path = f"uploads/products/{filename}"
            
            cursor.execute("UPDATE products SET image = %s WHERE id = %s", (db_path, p['id']))
            conn.commit()
            print("Updated.")
        except Exception as e:
            print(f"Download failed: {e}")
    else:
        print("No image found.")
    time.sleep(1)

cursor.close()
conn.close()
