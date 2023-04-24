var RTC_Event = /** @class */ (function () {
    function RTC_Event() {
        this.listeners = {
            'on': {},
            'once': {}
        };
    }
    RTC_Event.prototype.on = function (name, listener) {
        if (!this.listeners['on'][name]) {
            this.listeners['on'][name] = [];
        }
        this.listeners['on'][name].push(listener);
    };
    RTC_Event.prototype.once = function (name, listener) {
        if (!this.listeners['once'][name]) {
            this.listeners['once'][name] = [];
        }
        this.listeners['once'][name].push(listener);
    };
    RTC_Event.prototype.dispatch = function (name, data) {
        if (data === void 0) { data = []; }
        var regularEvent = this.listeners['on'];
        if (regularEvent.hasOwnProperty(name)) {
            regularEvent[name].forEach(function (listener) {
                listener.apply(void 0, data);
            });
        }
        var onceEvent = this.listeners['once'];
        if (onceEvent.hasOwnProperty(name)) {
            onceEvent[name].forEach(function (listener) {
                listener(data);
            });
            delete onceEvent[name];
        }
    };
    return RTC_Event;
}());
var RTC_Websocket = /** @class */ (function () {
    function RTC_Websocket(wsUri, options) {
        if (options === void 0) { options = []; }
        var _this = this;
        this.wsUri = wsUri;
        this.options = options;
        this.reconnectionInterval = 1000;
        this.connectionState = 'standby';
        this.willReconnect = true;
        this.defaultAuthToken = null;
        this.event = new RTC_Event();
        // HANDLE MESSAGE/EVENT DISPATCH WHEN DOM FINISHED LOADING
        this.onReady(function () {
            // Inspect messages and dispatch event
            _this.onMessage(function (payload) {
                if (payload.event) {
                    // Dispatch unfiltered event events
                    _this.event.dispatch('event', [payload]);
                    // Dispatch filtered event event
                    _this.event.dispatch('event.' + payload.event, [payload]);
                }
            });
        });
    }
    /**
     * Check if connection is opened
     * @returns {boolean}
     */
    RTC_Websocket.prototype.isOpened = function () {
        return 'open' === this.connectionState;
    };
    ;
    /**
     * Gets server connection state
     * @returns {string}
     */
    RTC_Websocket.prototype.getState = function () {
        return this.connectionState;
    };
    ;
    /**
     * Get browser implementation of WebSocket object
     * @return {WebSocket}
     */
    RTC_Websocket.prototype.getWebSocket = function () {
        return this.websocket;
    };
    ;
    /**
     * This event fires when a connection is opened/created
     * @param listener
     */
    RTC_Websocket.prototype.onOpen = function (listener) {
        this.event.on('open', listener);
        return this;
    };
    ;
    /**
     * This event fires when message is received
     * @param listener
     */
    RTC_Websocket.prototype.onMessage = function (listener) {
        this.event.on('message', function (payload) {
            if ('string' === typeof payload.data) {
                listener(JSON.parse(payload.data), payload);
            }
            else {
                listener(payload, payload);
            }
        });
        return this;
    };
    ;
    /**
     * Listens to filtered websocket event message
     *
     * @param event {string}
     * @param listener {callback}
     */
    RTC_Websocket.prototype.onEvent = function (event, listener) {
        this.event.on('event.' + event, listener);
        return this;
    };
    ;
    /**
     * Listens to RTC socket event
     *
     * @param listener
     */
    RTC_Websocket.prototype.onAnyEvent = function (listener) {
        this.event.on('event', listener);
        return this;
    };
    ;
    /**
     * This event fires when this connection is closed
     *
     * @param listener
     */
    RTC_Websocket.prototype.onClose = function (listener) {
        this.event.on('close', listener);
        return this;
    };
    ;
    /**
     * This event fires when client is disconnecting this connection
     *
     * @param listener
     */
    RTC_Websocket.prototype.onDisconnect = function (listener) {
        this.event.on('custom.disconnect', listener);
        return this;
    };
    ;
    /**
     * This event fires when an error occurred
     * @param listener
     */
    RTC_Websocket.prototype.onError = function (listener) {
        this.event.on('error', listener);
        return this;
    };
    ;
    /**
     * This event fires when this connection is in connecting state
     * @param listener
     */
    RTC_Websocket.prototype.onConnecting = function (listener) {
        this.event.on('connecting', listener);
        return this;
    };
    ;
    /**
     * This event fires when this reconnection is in connecting state
     * @param listener
     */
    RTC_Websocket.prototype.onReconnecting = function (listener) {
        this.event.on('reconnecting', listener);
        return this;
    };
    ;
    /**
     * This event fires when this reconnection has been reconnected
     * @param listener
     */
    RTC_Websocket.prototype.onReconnect = function (listener) {
        this.event.on('reconnect', listener);
        return this;
    };
    ;
    RTC_Websocket.prototype.onReady = function (listener) {
        window.addEventListener('DOMContentLoaded', listener);
    };
    ;
    /**
     * Set reconnection interval
     * @param interval
     */
    RTC_Websocket.prototype.setReconnectionInterval = function (interval) {
        this.reconnectionInterval = interval;
        return this;
    };
    ;
    /**
     * Set an authentication token that will be included in each outgoing message
     *
     * @param token {string} authentication token
     */
    RTC_Websocket.prototype.setAuthToken = function (token) {
        this.defaultAuthToken = token;
        return this;
    };
    ;
    /**
     * Manually reconnect this connection
     */
    RTC_Websocket.prototype.reconnect = function () {
        var _this = this;
        this.closeConnection(true);
        if (this.reconnectionInterval) {
            this.reconnectionTimeout = setTimeout(function () { return _this.createSocket(true); }, this.reconnectionInterval);
        }
    };
    ;
    /**
     * Connect to websocket server
     *
     * @returns {RTC_Websocket}
     */
    RTC_Websocket.prototype.connect = function () {
        // Create websocket connection
        this.createSocket();
        return this;
    };
    ;
    /**
     * Close this connection, the connection will not be reconnected.
     */
    RTC_Websocket.prototype.close = function () {
        this.willReconnect = false;
        this.closeConnection(false);
        clearTimeout(this.reconnectionTimeout);
        this.event.dispatch('custom.disconnect');
    };
    ;
    /**
     * Send message to websocket server
     * @param event {any} event name
     * @param message {array|object|int|float|string} message
     * @return Promise
     */
    RTC_Websocket.prototype.send = function (event, message) {
        var _this = this;
        if (message === void 0) { message = {}; }
        event = JSON.stringify({
            event: event,
            message: message,
            time: new Date().getTime(),
            token: this.defaultAuthToken
        });
        //Send message
        return new Promise(function (resolve, reject) {
            //Only send message when client is connected
            if (_this.isOpened()) {
                try {
                    _this.websocket.send(event);
                    resolve(_this);
                }
                catch (error) {
                    reject(error);
                }
                //Send message when connection is recovered
            }
            else {
                _this.log('Your message will be sent when server connection is recovered!');
                _this.event.once('open', function () {
                    try {
                        _this.websocket.send(event);
                        resolve(_this);
                    }
                    catch (error) {
                        reject(error);
                    }
                });
            }
        });
    };
    ;
    RTC_Websocket.prototype.log = function (message) {
        console.log(message);
    };
    ;
    RTC_Websocket.prototype.changeState = function (stateName, event) {
        this.connectionState = stateName;
        if ('close' === stateName && this.willReconnect) {
            this.reconnect();
        }
        this.event.dispatch(stateName, [event]);
    };
    ;
    RTC_Websocket.prototype.closeConnection = function (reconnect) {
        if (reconnect === void 0) { reconnect = false; }
        if (reconnect) {
            this.willReconnect = true;
            this.connectionState = 'internal_reconnection';
        }
        this.websocket.close();
    };
    ;
    RTC_Websocket.prototype.createSocket = function (isReconnecting) {
        var _this = this;
        if (isReconnecting === void 0) { isReconnecting = false; }
        if (true === isReconnecting) {
            this.connectionState = 'reconnecting';
            this.event.dispatch('reconnecting');
        }
        else {
            this.connectionState = 'connecting';
            this.event.dispatch('connecting');
        }
        if (this.wsUri.indexOf('ws://') === -1 && this.wsUri.indexOf('wss://') === -1) {
            this.wsUri = 'ws://' + window.location.host + this.wsUri;
        }
        this.websocket = new WebSocket(this.wsUri, []);
        this.websocket.addEventListener('open', function () {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            if ('reconnecting' === _this.connectionState) {
                _this.event.dispatch('reconnect');
            }
            _this.changeState('open', args);
        });
        this.websocket.addEventListener('message', function () {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            _this.event.dispatch('message', args);
        });
        this.websocket.addEventListener('close', function () {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            _this.changeState('close', args);
        });
        this.websocket.addEventListener('error', function () {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            _this.changeState('error', args);
        });
    };
    return RTC_Websocket;
}());