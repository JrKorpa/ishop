#/bin/sh
echo $@
list=$@
for i in $list;
do
STORE_ID=$i
cp goods.ini ./goods_$STORE_ID.ini;
sed -i "1s/.*/project.name = goods_$STORE_ID/" ./goods_$STORE_ID.ini;
done
