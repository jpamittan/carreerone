
// browser compatibility: get method for event 
    // addEventListener(FF, Webkit, Opera, IE9+) and attachEvent(IE5-8)
    var myEventMethod = 
        window.addEventListener ? 'addEventListener' : 'attachEvent';
    // create event listener
    var myEventListener = window[myEventMethod];
    // browser compatibility: attach event uses onmessage
    var myEventMessage = 
        myEventMethod == 'attachEvent' ? 'onmessage' : 'message';
    // register callback function on incoming message
    myEventListener(myEventMessage, function (e) {
        
        // we will get a string (better browser support) and validate
        // if it is an int - set the height of the iframe #my-iframe-id
        if (e.data.height && e.data.height === parseInt(e.data.height))
            document.getElementById('c1_' + e.data.module).style.height = e.data.height + 'px';
    }, false);