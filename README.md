# 110612304 

## [網站](http://yhl.daoyidh.com/ "KinmenCorpus")
說明： 協助老師展示其研究資料及成果，目前仍在開發中，前端後端都是我，[github](https://github.com/Sayoko123f/cmx)。

環境： PHP Laravel 7, Vue, MariaDB

帳號供測試： ***tt@test.com***, password: ***12345678***
### 功能
- 會員登入：支援網站原生會員，第三方支援 ***Google OAuth***。
- 權限管理：分三級權限， ***User, Manager, Admin***。
- 圖表：使用 ***Chart.js*** 函式庫。
- 內部資料搜尋(建置中)

---
## [爬蟲：金門日報](https://github.com/Sayoko123f/yang)
目的： 協助老師蒐集語料供研究使用。

說明： 爬蟲程式碼在 ***kmdn.php***，其他資料夾包含一些資料處理的程式碼。

環境： PHP, include library simple_html_dom

成果： 抓下約兩萬六千筆文章放入 MYSQL，約三千六百萬中文字數。  

---
## 爬蟲：漫畫網站
目的： 練習 PHP 抽象類別。

說明： 支援三個漫畫網站，可抓取圖片和詮釋資料。

環境： PHP, include library simple_html_dom

架構： 程式入口為 ***main.php***，檢查輸入的網址是否支援，抽象類別
***MasterCrawler*** 位於 ***master.php*** 提供三個實作類別繼承。