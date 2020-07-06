// Validation errors messages for Parsley
// Load this after Parsley

Parsley.addMessages('ka', {
    defaultMessage: "შეიყვანეთ სწორი ფორმატით.",
    type: {
        email: "შეიყვანეთ ელ-ფოსტა სწორი ფორმატით.",
        url: "შეიყვანეთ ბმული სწორი ფორმატით.",
        number: "შეიყვანეთ ნომერი სწორი ფორმატით.",
        integer: "შეიყვანეთ მხოლოდ მთელი რიცხვები.",
        digits: "შეიყვანეთ მხოლოდ ციფრები.",
        alphanum: "შეიყვანეთ მხოლოდ ციფრები და ასოები."
    },
    notblank: "მნიშვნელობა არ შეიძლება იყოს ცარიელი.",
    required: "შევსება აუცილებელია.",
    pattern: "ფორმატი არასწორია.",
    min: "მნიშვნელობა უნდა აღემატებოდეს ან უდრიდეს %s-ს.",
    max: "მნიშვნელობა არ უნდა აღემატებოდეს ან უდრიდეს %s-ს.",
    range: "მნიშვნელობა უნდა იყოს %s და %s შორის.",
    minlength: "მნიშვნელობა უნდა შეიცავდეს მინიმუმ %s სიმბოლოს.",
    maxlength: "მნიშვნელობა უნდა შეიცავდეს მაქსიმუმ %s სიმბოლოს.",
    length: "მნიშვნელობა უნდა შეიცავდეს მინიმუმ %s და მაქსიმუმ %s სიმბოლოს.",
    mincheck: "აირჩიეთ მინიმუმ %s მნიშვნელობა.",
    maxcheck: "აირჩიეთ მაქსიმუმ %s მნიშვნელობა.",
    check: "აირჩიეთ მინიმუმ %s და მაქსიმუმ %s მნიშვნელობა.",
    equalto: "მნიშვნელობები უნდა ემთხვეოდეს ერთმანეთს."
});

Parsley.setLocale('ka');
