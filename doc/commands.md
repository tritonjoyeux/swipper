<h1>Commands</h1>

## Inditex
```
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country portugal --extras='{"domaine":"pt", "code_pays":"pt", "lang":10, "sector": 1, "brands": ["zara"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country portugal --extras='{"domaine":"pt", "code_pays":"pt", "lang":10, "sector": 1, "brands": ["inditex"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country portugal --extras='{"domaine":"pt", "code_pays":"pt", "lang":10, "sector": 1, "brands": ["oysho"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country portugal --extras='{"domaine":"pt", "code_pays":"pt", "lang":10, "sector": 1, "brands": ["zara home"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country portugal --extras='{"domaine":"pt", "code_pays":"pt", "lang":10, "sector": 1, "brands": ["bershka"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country portugal --extras='{"domaine":"pt", "code_pays":"pt", "lang":10, "sector": 1, "brands": ["pull and bear"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country portugal --extras='{"domaine":"pt", "code_pays":"pt", "lang":10, "sector": 1, "brands": ["massimo dutti"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country portugal --extras='{"domaine":"pt", "code_pays":"pt", "lang":10, "sector": 1, "brands": ["uterqüe"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country portugal --extras='{"domaine":"pt", "code_pays":"pt", "lang":10, "sector": 1, "brands": ["stradivarius"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country netherland --extras='{"domaine":"nl", "code_pays":"nl", "lang":1, "sector": 1, "brands": ["zara"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country netherland --extras='{"domaine":"nl", "code_pays":"nl", "lang":1, "sector": 1, "brands": ["stradivarius"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country netherland --extras='{"domaine":"nl", "code_pays":"nl", "lang":1, "sector": 1, "brands": ["zara home"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country netherland --extras='{"domaine":"nl", "code_pays":"nl", "lang":1, "sector": 1, "brands": ["bershka"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country netherland --extras='{"domaine":"nl", "code_pays":"nl", "lang":1, "sector": 1, "brands": ["pull and bear"]}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country china --extras='{"domaine":"cn", "code_pays":"cn", "lang":5 , "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country hk --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country japan --extras='{"domaine":"jp", "code_pays":"jp", "lang":8, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country australia --extras='{"domaine":"au", "code_pays":"au", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country taiwan --extras='{"domaine":"jp", "code_pays":"jp", "lang":8, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent inditex_careers --writers api --country china --extras='{"domaine":"tw", "code_pays":"tw", "lang":5, "sector": 1}' -v
```

## Cos

```
php -d mbstring.func_overload=0 bin/swiper run --agent cos --country cn --writers api --freshness 10 --extras='{"domaine":"cn", "code_pays":"cn", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent cos --country au --writers api --freshness 10 --extras='{"domaine":"au", "code_pays":"au", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent cos --country hk --writers api --freshness 10 --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent cos --country jp --writers api --freshness 10 --extras='{"domaine":"jp", "code_pays":"jp", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent cos --country kr --writers api --freshness 10 --extras='{"domaine":"kr", "code_pays":"kr", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent cos --country sg --writers api --freshness 10 --extras='{"domaine":"sg", "code_pays":"sg", "lang":1, "sector": 1}' -v
```


## Style Careers
```
php -d mbstring.func_overload=0 bin/swiper run --agent style_careers --writers api --freshness 5 --extras='{"domaine":"us", "code_pays":"us", "lang":1, "sector": 1}' -v
```

## Career Next
```
php -d mbstring.func_overload=0 bin/swiper run --agent career_next --writers api --freshness 5 --extras='{"domaine":"uk", "code_pays":"uk", "lang":1, "sector": 1}' -v
```

## Moiselle
```
php -d mbstring.func_overload=0 bin/swiper run --agent moiselle --writers api --freshness 10 --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 1}' -v
```

## Jobs db
```
php -d mbstring.func_overload=0 bin/swiper run --agent jobs_db --writers api --freshness 10 --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 1}' -v
```

## JbStyle
```
php -d mbstring.func_overload=0 bin/swiper run --agent jbstyle --writers api --freshness 1 --extras='{"domaine":"us", "code_pays":"us", "lang":1, "sector": 1}' -v
```

## Randa
```
php -d mbstring.func_overload=0 bin/swiper run --agent randa --writers api --freshness 2 --extras='{"domaine":"us", "code_pays":"us", "lang":1, "sector": 1}' -v
```

## Besteam
```
php -d mbstring.func_overload=0 bin/swiper run --agent besteam --writers api  --freshness 2 --terms=fashion --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent besteam --writers api --freshness 2 --terms=garment --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent besteam --writers api --freshness 2 --terms=apparel --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent besteam --writers api --freshness 2 --terms=textile --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 1}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent besteam --writers api --freshness 2 --terms=luxury --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 2}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent besteam --writers api --freshness 2 --terms=cosmetic --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 3}' -v
php -d mbstring.func_overload=0 bin/swiper run --agent besteam --writers api --freshness 2 --terms=beauty --extras='{"domaine":"hk", "code_pays":"hk", "lang":1, "sector": 3}' -v
```

## Peek & Cloppenburg
```
php -d mbstring.func_overload=0 bin/swiper run --agent peek_cloppenburg --writers api --freshness 2 --extras='{"domaine":"at", "code_pays":"at", "lang":1, "sector": 1}' -v
```

## Biba
```
php -d mbstring.func_overload=0 bin/swiper run --agent biba --writers api --extras='{"domaine":"de", "code_pays":"de", "lang":4, "sector": 1}' -v
```

## Bestseller
```
php -d mbstring.func_overload=0 bin/swiper run --agent bestseller --writers api -v --extras='{"domaine":"de", "code_pays":"de", "lang":4, "sector": 1, "brand": "Vero Moda"}'
php -d mbstring.func_overload=0 bin/swiper run --agent bestseller --writers api -v --extras='{"domaine":"de", "code_pays":"de", "lang":4, "sector": 1, "brand": "Jack & Jones"}'
php -d mbstring.func_overload=0 bin/swiper run --agent bestseller --country de --writers api -v --extras='{"domaine":"de", "code_pays":"de", "lang":4, "sector": 1, "brand": "Only"}'
php -d mbstring.func_overload=0 bin/swiper run --agent bestseller --country au --writers api -v --extras='{"domaine":"de", "code_pays":"de", "lang":4, "sector": 1, "brand": "Only"}'
```

## Career HM
```
php -d mbstring.func_overload=0 bin/swiper run --agent career_hm --writers api -v --extras='{"domaine":"mx", "code_pays":"mx", "lang":2, "sector": 1, "brand": "Only"}'
```

## Superdry
```
php  -d mbstring.func_overload=0 bin/swiper run --agent superdry -v --writers api --extras='{"domaine":"uk", "code_pays":"uk", "lang":1, "sector": 1}' --freshness 10
```