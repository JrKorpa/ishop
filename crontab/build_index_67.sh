#!/bin/sh
list="60 74 84 91 101 102 103 104 105 109 110 111 113 116 117 121 123 125 128 129 130 133 134 135 136 138 141 143 145 146 147 148 152 153 155 157 158 161 162 165 166 167 168 169 171 172 173 174 175 177 178 180 183 185 186 187 191 192 193 194 198 199 201 202 204 205 206 207 210 211 212 214 215 218 219 221 222 223 224 228 229 230 233 234 236 237 238 239 243 244 246 247 403"
if [ "$#" -gt "0" ];then
    list=$@
fi

for i in $list;
do
/usr/bin/php /data/www/crontab/index.php boss_style build update $i;
sleep 10s;
done