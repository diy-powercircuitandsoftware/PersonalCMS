class CompilerCSScriptWA {
    Keywords = ["abstract", "add", "as", "ascending",
        "async", "await", "base", "bool", "break", "by",
        "byte", "case", "catch", "char", "checked", "class",
        "const", "continue", "decimal", "default", "delegate",
        "descending", "do", "double", "dynamic", "else", "enum",
        "equals", "explicit", "extern", "false", "finally", "fixed",
        "float", "for", "foreach", "from", "get", "global", "goto", "group",
        "if", "implicit", "in", "int", "interface", "internal", "into", "is",
        "join", "let", "lock", "long", "namespace", "new", "null", "object",
        "on", "operator", "orderby", "out", "override", "params", "partial",
        "private", "protected", "public", "readonly", "ref", "remove", "return",
        "sbyte", "sealed", "select", "set", "short", "sizeof", "stackalloc",
        "static", "string", "struct", "switch", "this", "throw", "true", "try",
        "typeof", "uint", "ulong", "unchecked", "unsafe", "ushort", "using",
        "value", "var", "virtual", "void", "volatile", "where", "while", "yield"];
    Separators = [' ', '{', '}', '(', ')', '[', ']', '.', '\n', ':', ';', ','];
    Operators = ["-", "+", "/*", "^", "~", "!", "%", "<", ">", "=", "is", "as", "&", "|"];
    Tokenize(inputString) {
        const characters = inputString.split("");
        var tokens = [];
        let lexeme = '';

        const whitespace = " ";
         
        

        characters.forEach((character, i) => {
            if (character != whitespace) {
                //terminator
                if (this.Separators.indexOf(character) >= 0 || this.Operators.indexOf(character) >= 0) {
                    if (lexeme != "") {
                        tokens.push(lexeme);
                    }
                    tokens.push(character);
                    lexeme = '';
                } else {
                    lexeme += character;
                }
            } else if (character == whitespace) {
                if (lexeme != "") {
                    tokens.push(lexeme);
                }
                lexeme = '';
            }
        });
        return tokens;
    }
    Analyse(tokens) {
        const tokenAnalysis = [];
        
        tokens.forEach((token, i) => {
            const term = {}
            term.position = (i + 1);
            //scan keywords
            if (this.Keywords.indexOf(token) >= 0) {
                term.value = token;
                term.group = "keyword";
            }
            //scan separators
            else if (this.Separators.indexOf(token) >= 0) {
                term.value = token;
                term.group = "separator";
            }
            //scan operators
            else if (this.Operators.indexOf(token) >= 0) {
                term.value = token;
                term.group = "operator";
            }
            //scan constants
            else if (!isNaN(token)) {
                term.value = token;
                term.group = "constant";
            }
            //scan identifier
            else {
                term.value = token;
                term.group = "identifier";
            }
            tokenAnalysis.push(term);
        });
        console.log(tokenAnalysis)
        return tokenAnalysis;
    }

}




