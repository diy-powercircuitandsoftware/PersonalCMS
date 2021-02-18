class Statistical_Basic {
    Average(arr) {
        return  this.Sum(arr) / arr.length;
    }
    Bell_Above(za2, arr) {
        var z = this.Z(Math.abs(parseFloat(za2)), arr);
        return Math.round(this.ZTable(z) * 10000) / 10000;
    }
    Bell_Below(za2, arr) {
        var z = this.Z(Math.abs(parseFloat(za2)), arr);
        var p = 1 - this.ZTable(z);
        return Math.round(p * 10000) / 10000;
    }
    Bell_Between(za2l, za2u, arr) {
        var z1 = this.Z((parseFloat(za2l)), arr);
        var z2 = this.Z((parseFloat(za2u)), arr);
        var zp = this.ZTable(z2) - this.ZTable(z1);
        return Math.round(zp * 10000) / 10000;
    }
    Bell_Outside(za2l, za2u, arr) {
        var z1 = this.Z((parseFloat(za2l)), arr);
        var z2 = this.Z((parseFloat(za2u)), arr);
        var zp = this.ZTable(z1) + (1 - this.ZTable(z2));
        return Math.round(zp * 10000) / 10000;
    }
    ConfidenceLevelTOZA2(CL) {
        var a2 = (1 - CL) / 2;
        return this.ZTableInvert(a2);
    }
    GeometricMean(arr) {
        return Math.pow(arr.reduce(function (a, b) {
            return a * b;
        }), 1 / arr.length);
    }
    HarmonicMean(arr) {
        var data = 0.00;
        for (var i = 0; i < arr.length; i++) {
            data = data + (1 / parseFloat(arr[i]));
        }
        return arr.length / data;
    }
    Mid(arr) {
        arr.sort(function (a, b) {
            return a - b;
        });
        var middle = Math.floor(arr.length / 2);
        if (arr.length % 2 === 0) {
            return (arr[middle - 1] + arr[middle]) / 2;
        }
        return arr[middle];
    }
    MidRange(arr) {
        return this.Range(arr) / 2;
    }
    MeanDeviation(arr) {
        var avg = this.Average(arr);
        return  arr.reduce(function (sum, num) {
            return sum + Math.abs(num - avg);
        }, 0) / (arr.length);
    }

    Mode(arr) {
        var counter = {};
        var mode = [];
        var max = 0;
        for (var i in arr) {
            if (!(arr[i] in counter))
                counter[arr[i]] = 0;
            counter[arr[i]]++;

            if (counter[arr[i]] == max)
                mode.push(arr[i]);
            else if (counter[arr[i]] > max) {
                max = counter[arr[i]];
                mode = [arr[i]];
            }
        }
        return mode;
    }

    Quantile(q, arr) {
        arr.sort(function (a, b) {
            return a - b;
        });
        var pos = ((arr.length + 1) * q);
        var fraction = pos - Math.floor(pos);
        if (fraction == 0) {
            return arr[pos - 1];
        } else {
            var diff = arr[Math.floor(pos)] - arr[Math.floor(pos) - 1];
            var arithmetic = diff * fraction;
            return arr[Math.floor(pos) - 1] + arithmetic;
        }

    }

    PopulationStandardDeviation(arr) {
        var m = this.Average(arr);
        return Math.sqrt(arr.reduce(function (sq, n) {
            return sq + Math.pow(n - m, 2);
        }, 0) / (arr.length));
    }

    Range(arr) {
        return Math.max.apply(null, arr) - Math.min.apply(null, arr);
    }

    StandardDeviation(arr) {
        var m = this.Average(arr);
        return Math.sqrt(arr.reduce(function (sq, n) {
            return sq + Math.pow(n - m, 2);
        }, 0) / (arr.length - 1));
    }

    StandardError(arr) {
        return this.StandardDeviation(arr) / Math.sqrt(arr.length);
    }

    Sum(arr) {
        return arr.reduce(function (a, b) {
            return a + b;
        });
    }

    Variance(arr) {
        return Math.pow(this.StandardDeviation(arr), 2);
    }

    Z(v, arr) {
        return  (v - this.Average(arr)) / this.StandardDeviation(arr);
    }

    ZTable(z) {
        if (z < -7) {
            return 0.0;
        }
        if (z > 7) {
            return 1.0;
        }
        z = Math.abs(z);
        var b = 0.0;
        var s = Math.sqrt(2) / 3 * z;
        var HH = .5;
        for (var i = 0; i < 12; i++) {
            var a = Math.exp(-HH * HH / 9) * Math.sin(HH * s) / HH;
            b = b + a;
            HH = HH + 1.0;
        }
        var p = .5 - b / Math.PI;
        if (z >= 0.0) {
            return 1.0 - p;
        }
        return p;
    }

    ZTableInvert(p) {
        var t, v, theSign;
        if (p >= 1) {
            return 7;
        } else if (p <= 0) {
            return -7;
        }
        if (p < .5) {
            t = p;
            theSign = -1;
        } else
        {
            t = 1 - p;
            theSign = 1;
        }
        v = Math.sqrt(-2.0 * Math.log(t));
        var x = 2.515517 + (v * (0.802853 + v * 0.010328));
        var y = 1 + (v * (1.432788 + v * (0.189269 + v * 0.001308)));
        var Q = theSign * (v - (x / y));
        return Q;
    }

}