import urllib.request
import re
import os
import json
import uuid
import time
import random

def fetch_html(url):
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    try:
        return urllib.request.urlopen(req).read().decode('utf-8')
    except Exception as e:
        print(f"Error fetching {url}: {e}")
        return ""

def download_image(url, save_path):
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    try:
        with urllib.request.urlopen(req) as response, open(save_path, 'wb') as out_file:
            out_file.write(response.read())
        return True
    except Exception as e:
        print(f"Error downloading {url}: {e}")
        return False

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
STORAGE_DIR = os.path.join(BASE_DIR, 'storage', 'app', 'public', 'cars', 'scraped')
DATA_DIR = os.path.join(BASE_DIR, 'database', 'data')

os.makedirs(STORAGE_DIR, exist_ok=True)
os.makedirs(DATA_DIR, exist_ok=True)

makes = ['toyota', 'honda', 'ford', 'mercedes-benz', 'hyundai', 'nissan']
cars = []

for make in makes:
    print(f"Scraping {make}...")
    html = fetch_html(f"https://www.auctionexport.com/en/cars/{make}")
    
    links = re.findall(r'href="(/en/Inventory/Info/[^"]+)"', html)
    links = list(set(links))[:10]
    
    for link in links:
        car_url = f"https://www.auctionexport.com{link}"
        car_html = fetch_html(car_url)
        time.sleep(0.5)
        
        if not car_html:
            continue
            
        slug_match = re.search(r'/Info/(\d{4})-([^-]+)-([^-]+)', link)
        if not slug_match:
            continue
            
        year = slug_match.group(1)
        car_make = slug_match.group(2).title()
        model = slug_match.group(3).title()
        
        price = 0
        price_match = re.search(r'\$(\d+,\d+|\d+)', car_html)
        if price_match:
            price_str = price_match.group(1).replace(',', '')
            price = int(price_str) * 100
        
        if price == 0:
            price = random.randint(5000, 50000) * 100
        else:
            # Add a random variance of +/- 15% to make them more varied
            variance = random.uniform(0.85, 1.15)
            price = int(price * variance)
            
        mileage = 0
        mileage_match = re.search(r'Mileage[^\d]+([\d,]+)', car_html, re.IGNORECASE)
        if mileage_match:
            mileage = int(mileage_match.group(1).replace(',', ''))
            
        images_urls = re.findall(r'<img[^>]+src="([^"]+)"[^>]*>', car_html)
        car_images = [img for img in images_urls if "picID=" in img]
        
        # Deduplicate and fix amp
        unique_images = []
        seen_pic_ids = set()
        for img in car_images:
            pic_id_match = re.search(r'picID=(\d+)', img)
            if pic_id_match:
                pic_id = pic_id_match.group(1)
                if pic_id not in seen_pic_ids:
                    seen_pic_ids.add(pic_id)
                    unique_images.append(img)
                    
        if not unique_images:
            continue
            
        # Get up to 6 unique images
        target_images = unique_images[:6]
        image_paths = []
        
        print(f"Downloading {len(target_images)} images for {year} {car_make} {model}")
        for img_url in target_images:
            clean_url = img_url.replace('&amp;', '&')
            clean_url = re.sub(r'width=\d+', 'width=640', clean_url)
            clean_url = re.sub(r'height=\d+', 'height=640', clean_url)
            
            filename = f"{uuid.uuid4().hex}.jpg"
            save_path = os.path.join(STORAGE_DIR, filename)
            
            if download_image(clean_url, save_path):
                image_paths.append(f"cars/scraped/{filename}")
                
        if image_paths:
            cars.append({
                "year": year,
                "make": car_make,
                "model": model,
                "price_usd_cents": price,
                "mileage": mileage,
                "image_paths": image_paths
            })
            
        if len(cars) >= 40:
            break
            
    if len(cars) >= 40:
        break

json_path = os.path.join(DATA_DIR, 'scraped_cars.json')
with open(json_path, 'w') as f:
    json.dump(cars, f, indent=4)
    
print(f"Scraped {len(cars)} cars with multiple images successfully!")
