The idea is to capture data about clients and search bots and analyse it in future, with simple including and placing.  

# Requirements
Create mysql user and database, grant all rights for the user for created db. Set db name, login and password in to config.php
Next you have 2 ways

# 1. Simple way
Just copy whole folder to your website and copy paste code from index.php file to index.php of your web site. Be carefull do not replace your original index.php.

# 2. Harder and more flexible way
Place that whole folder for example in to /var/www/domain-stats/. 
Next in DomainStats.php append that path before each of file which is included/required.
Copy code from index.php and also append that full path to one "require" line.  
Now you can just paste only 2 lines from index.php to any domain or subdomain, instead of copying whole folder.