class Statistical_Basic {
    //https://github.com/chen0040/js-stats/blob/master/src/jsstats.js
    //https://stackoverflow.com/questions/36575743/how-do-i-convert-probability-into-z-score
    ////https://github.com/errcw/gaussian
    Average(arr) {
        return  this.Sum(arr) / arr.length;
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
    ND_CDF(x, arr) {
        var z = (x - this.Average(arr)) / (Math.SQRT2 * this.StandardDeviation(arr));
        return 0.5 + 0.5 * this.ND_ErrorFN(z);
    }
    ND_CDFInv(p, arr) {
        var Z = Math.SQRT2 * this.ND_ErrorFNInv(2 * p - 1);

        return Z * this.StandardDeviation(arr) + this.Average(arr);
    }
    ND_ErrorFN(z) {
        var t = 1.0 / (1.0 + 0.5 * Math.abs(z));
        // use Horner's method
        var ans = 1 - t * Math.exp(-z * z - 1.26551223 +
                t * (1.00002368 +
                        t * (0.37409196 +
                                t * (0.09678418 +
                                        t * (-0.18628806 +
                                                t * (0.27886807 +
                                                        t * (-1.13520398 +
                                                                t * (1.48851587 +
                                                                        t * (-0.82215223 +
                                                                                t * (0.17087277))))))))));
        if (z >= 0)
            return ans;
        else
            return -ans;
    }
    ND_ErrorFNInv(x) {

        var a = 0.147;
        var the_sign_of_x;
        if (x == 0)
        {
            return 0;
        }
        if (x > 0)
        {
            the_sign_of_x = 1;
        } else
        {
            the_sign_of_x = -1;
        }

        var ln_1minus_x_sqrd = Math.log(1 - x * x);
        var ln_1minusxx_by_a = ln_1minus_x_sqrd / a;
        var ln_1minusxx_by_2 = ln_1minus_x_sqrd / 2;
        var ln_etc_by2_plus2 = ln_1minusxx_by_2 + (2 / (Math.PI * a));
        var first_sqrt = Math.sqrt((ln_etc_by2_plus2 * ln_etc_by2_plus2) - ln_1minusxx_by_a);
        var second_sqrt = Math.sqrt(first_sqrt - ln_etc_by2_plus2);
        return second_sqrt * the_sign_of_x;

    }
    Quantile(q, arr) {
        arr.sort(function (a, b) {
            return a - b;
        });
        var pos = (arr.length - 1) * q;
        var base = Math.floor(pos);
        if (arr[base + 1] !== undefined) {
            return arr[base] + (pos - base) * (arr[base + 1] - arr[base]);
        } else {
            return arr[base];
        }
    }
    PDF(x, arr) {
        var m = this.StandardDeviation(arr) * Math.sqrt(2 * Math.PI);
        var e = Math.exp(-Math.pow(x - this.Average(arr), 2) / (2 * this.Variance(arr)));
        return e / m;
    }
    PPF(x, arr) {
        return  this.Average(arr) - this.StandardDeviation(arr) * Math.sqrt(2) * this.ND_ErrorFNInv(2 * x);
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
}
 