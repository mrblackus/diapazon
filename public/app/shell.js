/**
 * Created by mathieu.savy on 3/19/14.
 */

define(function (require) {
    var app = require('durandal/app'),
        ko = require('knockout');

    return {
        name: ko.observable(),
        sayHello: function() {
            app.showMessage('Hello ' + this.name() + '! Nice to meet you.', 'Greetings');
        }
    };
});