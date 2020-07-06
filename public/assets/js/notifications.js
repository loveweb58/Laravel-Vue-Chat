var neonNotifications = neonNotifications || {};

;(function ($, window, undefined) {
    "use strict";

    $(document).ready(function () {
        neonNotifications.$container = $("#notifications");
        neonNotifications.counter = 0;
        neonNotifications.messages = [];
        neonNotifications.add = function (id, text, date, unread, url, type, icon) {
            if (typeof unread === "undefined" || unread === null) {
                unread = true;
            }
            if (typeof url === "undefined" || url === null) {
                url = "#";
            }
            if (typeof type === "undefined" || type === null) {
                type = "info";
            }
            if (typeof icon === "undefined" || icon === null) {
                icon = "entypo-info";
            }
            if (typeof id === "undefined" || id === null) {
                if (neonNotifications.messages.length === 0) {
                    id = 0;
                } else {
                    id = (neonNotifications.messages.reduce(function (prev, current) {
                            if (+current.id > +prev.id) {
                                return current;
                            } else {
                                return prev;
                            }
                        }).id) + 1;
                }
            }
            //date = moment(date, 'YYYY-MM-DD hh:mm:ss').fromNow();
            neonNotifications.messages.push({'id': id});
            neonNotifications.$container.find('[data-variables="notifications"]')
                             .prepend('<li class="' + (unread ? 'unread ' : '') + 'notification-' + type + '" data-id="' + id + '">' +
                                 ' <a href="' + url + '">' +
                                 '<i class="' + icon + ' pull-right"></i>' +
                                 '<span class="line">' + (unread ? '<strong>' + text + '</strong> ' : text) + '</span>' +
                                 '<span class="line small">' + date + '</span>' +
                                 '</a></li>');
            if (unread) {
                neonNotifications.updateBadges(1);
            }
        };
        neonNotifications.remove = function (id) {
            if (neonNotifications.$container.find('[data-id="' + id + '"]').hasClass('unread')) {
                neonNotifications.updateBadges(-1);
            }
            neonNotifications.messages = neonNotifications.messages.filter(function (item) {
                return item.id !== id;
            });
            neonNotifications.$container.find('[data-id="' + id + '"]').remove();
        };
        neonNotifications.updateBadges = function (value) {
            neonNotifications.counter = neonNotifications.counter + value;
            if (neonNotifications.counter < 0) {
                neonNotifications.counter = 0;
            }
            neonNotifications.$container.find('[data-variables="counter"]').html(neonNotifications.counter);
            if (neonNotifications.counter == 0) {
                neonNotifications.$container.find('.badge[data-variables="counter"]').html('');
            }
        };

        $.extend(neonNotifications, {});
    });

})(jQuery, window);