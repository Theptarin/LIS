tb=`date '+%D %T'`
cd /var/www/service/LIS
php LISInfinity.php >> /var/www/service/LIS/LISInfinity.log
te=`date '+%D %T'`
echo "$tb , LISInfinity.sh working transfer , $te , LISInfinity.sh complete transfer " >>  /var/www/service/LIS/csv_LISInfinity.log
