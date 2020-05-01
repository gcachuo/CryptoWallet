mysqldump -h gcachuo.ml -ucachu -p --skip-set-charset --default-character-set=latin1 'crypto' -r "$(date +%Y%m%dT%H%M%S).cryptowallet.sql"
mysqldump -h localhost -u root -p --column-statistics=0 --skip-set-charset --default-character-set=latin1 'cryptowallet' -r "$(date +%Y%m%dT%H%M%S).cryptowallet.sql"

