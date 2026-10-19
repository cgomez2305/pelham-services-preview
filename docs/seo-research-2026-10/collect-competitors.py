import urllib.request, json, re, concurrent.futures
from html.parser import HTMLParser
from pathlib import Path
from datetime import datetime,timezone
OUT=Path(__file__).parent
class Extract(HTMLParser):
 def __init__(self):super().__init__();self.skip=0;self.text=[];self.links=[]
 def handle_starttag(self,t,a):
  if t in ('script','style','noscript'):self.skip+=1
  if t=='a':
   for k,v in a:
    if k=='href':self.links.append(v)
 def handle_endtag(self,t):
  if t in ('script','style','noscript'):self.skip=max(0,self.skip-1)
 def handle_data(self,d):
  if not self.skip and d.strip():self.text.append(d.strip())
def collect(item):
 name,url=item
 try:
  r=urllib.request.urlopen(urllib.request.Request(url,headers={'User-Agent':'Mozilla/5.0'}),timeout=30);raw=r.read().decode('utf-8','replace');p=Extract();p.feed(raw)
  data={'requested_url':url,'final_url':r.url,'status':r.status,'fetched_utc':datetime.now(timezone.utc).isoformat(),'text':'\n'.join(p.text),'links':list(dict.fromkeys(p.links))}
 except Exception as e:data={'requested_url':url,'error':str(e)}
 (OUT/(name+'.json')).write_text(json.dumps(data,ensure_ascii=False,indent=2));return name
if __name__=='__main__':
 import sys
 items=[x.split('=',1) for x in sys.argv[1:]] or [('edi-home','https://www.ediexpressinc.com'),('sterling-home','https://www.sterlingtransportation.com'),('daylight-home','https://www.dylt.com'),('jts-home','https://www.jtsexpress.com')]
 print(list(concurrent.futures.ThreadPoolExecutor().map(collect,items)))
