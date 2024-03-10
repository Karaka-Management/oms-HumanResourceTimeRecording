jsOMS.ready(function ()
{
    "use strict";

    const logo         = document.getElementById('login-logo');
    const cLogin       = document.getElementById('iCameraLoginButton');
    const pLogin       = document.getElementById('iPasswordLoginButton');
    const cancel       = document.getElementsByClassName('cancelButton');
    const cancelLength = cancel.length;
    let timer          = 10000;

    cLogin.addEventListener('click', function() {
        if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            const clock     = document.getElementById('iCameraCountdownClock');
            const countdown = document.getElementById('iCameraCountdown');
            const video     = document.getElementById('iVideoCanvas');

            navigator.mediaDevices.getUserMedia({ video: true, audio: false }).then(function(stream) {
                video.srcObject = stream;
                video.play();

                jsOMS.addClass(logo, 'vh');
                jsOMS.addClass(cLogin, 'vh');
                jsOMS.addClass(pLogin, 'vh');
                jsOMS.removeClass(document.getElementById('cameraLogin'), 'vh');

                timer           = 10000;
                clock.innerHTML = timer / 1000;

                jsOMS.removeClass(countdown, 'vh');

                const cameraTimer = setInterval(function() {
                    timer -= 100;

                    if (timer % 1000 === 0) {
                        clock.innerHTML = timer / 1000;
                    }

                    if (timer <= 0) {
                        jsOMS.addClass(document.getElementById('cameraLogin'), 'vh');
                        jsOMS.removeClass(cLogin, 'vh');
                        jsOMS.removeClass(pLogin, 'vh');
                        jsOMS.removeClass(logo, 'vh');

                        video.pause();
                        stream.getVideoTracks()[0].stop();

                        clearInterval(cameraTimer);
                    }
                }, 100);
            });
        }
    });

    pLogin.addEventListener('click', function() {
        const clock     = document.getElementById('iPasswordCountdownClock');
        const countdown = document.getElementById('iPasswordCountdown');

        jsOMS.addClass(pLogin, 'vh');
        jsOMS.addClass(cLogin, 'vh');
        jsOMS.removeClass(document.getElementById('passwordLogin'), 'vh');

        timer           = 10000;
        clock.innerHTML = timer / 1000;

        jsOMS.removeClass(countdown, 'vh');

        const passwordTimer = setInterval(function() {
            timer -= 100;

            if (timer % 1000 === 0) {
                clock.innerHTML = timer / 1000;
            }

            if (timer <= 0) {
                jsOMS.addClass(document.getElementById('passwordLogin'), 'vh');
                jsOMS.removeClass(pLogin, 'vh');
                jsOMS.removeClass(cLogin, 'vh');

                clearInterval(passwordTimer);
            }
        }, 100);
    });

    for (let i = 0; i < cancelLength; ++i) {
        cancel[i].addEventListener('click', function() {
            timer = 0;
        });
    }
});
