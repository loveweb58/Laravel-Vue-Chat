var charset7bit = ['@', '£', '$', '¥', 'è', 'é', 'ù', 'ì', 'ò', 'Ç', "\n", 'Ø', 'ø', "\r", 'Å', 'å', 'Δ', '_', 'Φ', 'Γ', 'Λ', 'Ω', 'Π', 'Ψ', 'Σ', 'Θ', 'Ξ', 'Æ', 'æ', 'ß', 'É', ' ', '!', '"', '#', '¤', '%', '&', "'", '(', ')', '*', '+', ',', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '<', '=', '>', '?', '¡', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ä', 'Ö', 'Ñ', 'Ü', '§', '¿', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ä', 'ö', 'ñ', 'ü', 'à'];
var charset7bitext = ["\f", '^', '{', '}', '\\', '[', '~', ']', '|', '€'];

function calculateSmsLength(content) {
    var chars_arr = content.split("");
    var coding = '7bit';
    var parts = 1;
    var part = 1;
    var chars_used = 0;
    var chars_sms = 160;
    for (i = 0; i < chars_arr.length; i++) {
        if (charset7bit.indexOf(chars_arr[i]) >= 0) {
            chars_used = chars_used + 1;
        } else if (charset7bitext.indexOf(chars_arr[i]) >= 0) {
            chars_used = chars_used + 2;
        } else {
            coding = '16bit';
            chars_used = chars_arr.length;
            break;
        }
    }
    if (coding == '7bit') {
        if (chars_used > 160) {
            parts = Math.ceil(chars_used / 153);
            var part_chars_used = 7;
            chars_sms = 153;
            for (i = 0; i < chars_arr.length; i++) {
                if (part_chars_used + 1 > 160) {
                    part = part + 1;
                    part_chars_used = 7;
                }
                if (charset7bitext.indexOf(chars_arr[i]) >= 0 && part_chars_used + 2 > 160) {
                    part = part + 1;
                    part_chars_used = 7;
                }
                if (charset7bitext.indexOf(chars_arr[i]) == -1) {
                    part_chars_used = part_chars_used + 1;
                } else {
                    part_chars_used = part_chars_used + 2;
                }
            }
        } else {
            chars_sms = 160;
        }
    } else {
        if (chars_used > 70) {
            parts = Math.ceil(chars_used / 67);
            part_chars_used = 3;
            chars_sms = 67;
            for (i = 0; i < chars_arr.length; i++) {
                if (part_chars_used + 1 > 70) {
                    part = part + 1;
                    part_chars_used = 3;
                }
                part_chars_used = part_chars_used + 1;
            }
        } else {
            chars_sms = 70;
        }
    }
    return {'parts': parts, 'coding': coding, 'chars_used': chars_used, 'chars_sms': chars_sms};
}