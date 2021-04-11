class ArrayQueryFW {
    Array_Value(...args) {

        if (args.length == 1) {
            return   Object.values(args[0]);
        } else if (args.length == 2 && Array.isArray(args[0])) {
            var arr = [];
            for (var i in args[0]) {
                arr.push(args[0][i][args[1]]);
            }
            return arr;
        } else if (args.length == 2 && typeof args[0] === 'object') {
            return  args[0][args[1]];
        }
    }
}