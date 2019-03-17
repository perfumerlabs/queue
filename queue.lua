#!/usr/bin/env tarantool

box.cfg {
    listen           = '0.0.0.0:3301';
    slab_alloc_arena = 1;
    wal_dir          = "/var/lib/tarantool";
    snap_dir         = "/var/lib/tarantool";
    vinyl_dir        = "/var/lib/tarantool";
    username         = "tarantool";
}
box.schema.user.grant('guest', 'read,write,execute', 'universe', nil, {if_not_exists = true})

queue = require('queue')
queue.start()
