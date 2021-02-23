var config = require('../config').redisConfig;
var cache = (require('redis')).createClient(config.port, config.host);
var prefix = config.prefix;
console.log("redis cache server connected");
exports.str_set = function(key, str, callback) {
    if(typeof callback == 'function') {
        callback = cache.print;
    }
    cache.set(prefix + key, str, callback)
};
exports.str_get = function(key, callback) {
    cache.get(prefix + key, callback);
};

exports.map_set = function(key, data, callback) {
    if(typeof callback == 'function') {
        callback = cache.print;
    }
    if(typeof data == "object") {
        cache.hmset(prefix + key, data, callback)
    }
    else {
        return false;
    }
};
exports.map_get = function(key, callback) {
    cache.hgetall(prefix + key, callback);
};

