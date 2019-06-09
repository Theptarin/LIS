tb=`date '+%D %T'`
cd /var/www/service/LIS
php LISIt1000.php >> /var/www/service/LIS/LISIt1000.log
te=`date '+%D %T'`
echo "$tb , LISIt1000.sh working transfer , $te , LISIt1000.sh complete transfer " >>  /var/www/service/LIS/csv_LISIt1000.log
#--------------------------------------------------------------------------------------------------------------------------------------------------#
# คำสั่งเดิมที่เคยใช้
# mv /var/www/mount/cobas-it-1000/his/RES/* /var/www/mount/hims-doc/cobas/RES/
#--------------------------------------------------------------------------------------------------------------------------------------------------#
