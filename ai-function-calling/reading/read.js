jQuery(document).ready(function ($) {
    let initNiceScroll = false;
    let autosave       = false;
    let saveInterval   = null;
    let saveAjax       = null;
    let remainingTime  = 0;

    // section switcher 
    $(document).on('click', '.question-palette__part', function (event, scrollDefault = true) {
        let partIndex = $(this).data('part');

        if (document.querySelectorAll(`.question-palette__part`).length > 0) {
            document.querySelectorAll(`.question-palette__part`).forEach((p) => {
                let total = p.querySelectorAll(`.question-palette__item`).length;
                let checked = p.querySelectorAll(`.question-palette__item.-checked`).length;
                if ($('.js-attempt-only-reading').length > 0 || $('.js-attempt-only-listening').length > 0 || $('.js-attempt-only-writing').length > 0) {
                    if (total === checked) {
                        p.classList.add('-finished');
                    } else {
                        p.classList.remove('-finished');
                    }
                }
            });



            if (!document.querySelector(`#navigation-bar-${partIndex}`).classList.contains('-active')) {
                $('.question-palette__part').removeClass('-active');
                $('.test-contents').removeClass('-show');
                $('.test-panel').removeClass('-show');
                if (document.querySelector(`#navigation-bar-${partIndex}`)) document.querySelector(`#navigation-bar-${partIndex}`).classList.add('-active');
                if (document.querySelector(`#part-questions-${partIndex}`)) document.querySelector(`#part-questions-${partIndex}`).classList.add('-show');
                if (document.querySelector(`#part-${partIndex}`)) document.querySelector(`#part-${partIndex}`).classList.add('-show');
                $(`.question-palette__item`).removeClass('is-selected');
                if ($(`#navigation-bar-${partIndex} .question-palette__item`).length > 0) {
                    $(`#navigation-bar-${partIndex} .question-palette__item`)[0].classList.add('is-selected');
                    let qNum = $(`#navigation-bar-${partIndex} .question-palette__item`)[0].dataset.num;
                    $(`.question__input[data-num="${qNum}"]`).focus();

                }
            }

        }
    });

    // question pallete
    $(document).on('click', '.question-palette__item', function (event) {

        // selected num
        let qNum        = $(this).data('num');
        let pNum        = $(this).data('p');
        let startNum    = $(this).data('start-num');
        let endNum      = $(this).data('end-num');


        $('.question-palette__item').removeClass('is-selected');
        $('.question-palette__part').removeClass('-active');
        $('.test-contents').removeClass('-show');
        $('.test-panel').removeClass('-show');


        if (document.querySelector(`#navigation-bar-${pNum}`)) document.querySelector(`#navigation-bar-${pNum}`).classList.add('-active');
        if (document.querySelector(`#part-questions-${pNum}`)) document.querySelector(`#part-questions-${pNum}`).classList.add('-show');
        if (document.querySelector(`#part-${pNum}`)) document.querySelector(`#part-${pNum}`).classList.add('-show');
        $(`.question-palette__item[data-num="${qNum}"]`).addClass('is-selected');
        // 
        $(`.question__input[data-num="${qNum}"]`)[0].scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        $(`.question__input[data-num="${qNum}"]`)[0].focus();

        /*
        setTimeout(() => {
            $(`.question__input[data-num="${qNum}"]`).focus();
        },555); 
        */
            
        

        if (+qNum > 1) {
            $('#js-btn-previous').removeClass('-disabled');
            $('#js-btn-previous').attr('disabled', false);
        } else {
            $('#js-btn-previous').addClass('-disabled');
            $('#js-btn-previous').attr('disabled', true);
        }

        if (+qNum < $(`.question-palette__item`).length) {
            $('#js-btn-next').removeClass('-disabled');
            $('#js-btn-next').attr('disabled', false);
        } else {
            $('#js-btn-next').addClass('-disabled');
            $('#js-btn-next').attr('disabled', true);
        }

      


    });

    $(document).on('click', '.question__input', function () {
        let qNum = $(this).data('num');
        $(`.question-palette__item[data-num="${qNum}"]`).click();
    });

    $(document).on('change keyup', '.question__input', function () {

        if ($('.js-attempt-only-reading').length > 0 || $('.js-attempt-only-listening').length > 0 || $('.js-attempt-only-writing').length > 0) {

            let qNum = $(this).data('num');
            let inputType = $(this).data('input_type');
            let total = $(this).data('q_total');
            $(`.question-palette__item[data-num="${qNum}"]`).click();


            if (inputType === "select") {
                if ($(this).val() !== "") {
                    $(`.question-palette__item[data-num="${qNum}"]`).addClass('-checked');
                } else {
                    $(`.question-palette__item[data-num="${qNum}"]`).removeClass('-checked');
                }
            } else if (inputType === "fillup") {

                if ($(this).val() !== "" || $(this).val().trim().length > 0) {
                    $(`.question-palette__item[data-num="${qNum}"]`).addClass('-checked');
                } else {
                    $(`.question-palette__item[data-num="${qNum}"]`).removeClass('-checked');
                }

            } else if (inputType === "radio") {
                if ($(`[name="q-${qNum}"]:checked`).length > 0) {
                    $(`.question-palette__item[data-num="${qNum}"]`).addClass('-checked');
                } else {
                    $(`.question-palette__item[data-num="${qNum}"]`).removeClass('-checked');
                }

            } else if (inputType === "textarea") {
                window.countWords($(this));
                if ($(this).val() !== "" || $(this).val().trim().length > 0) {
                    $(`.question-palette__item[data-num="${qNum}"]`).addClass('-checked');
                } else {
                    $(`.question-palette__item[data-num="${qNum}"]`).removeClass('-checked');

                }
            } else if (inputType === "checkbox") {

                if($(`[name="q-${qNum}"]:checked`).length <= total ) {

                } else {
                    $(this).prop('checked', false);
                }


                if ($(`[name="q-${qNum}"]:checked`).length > 0) {
                    $(`.question-palette__item[data-num="${qNum}"]`).addClass('-checked');
                } else {
                    $(`.question-palette__item[data-num="${qNum}"]`).removeClass('-checked');
                }
            }



            let pNum = $(`.question-palette__item[data-num="${qNum}"]`).data('p');

            if (document.querySelector(`#navigation-bar-${pNum}`)) {
                let totalAttempted = document.querySelector(`#navigation-bar-${pNum}`).querySelectorAll('.question-palette__item.-checked').length;
                if (document.querySelector(`#navigation-bar-${pNum}`).querySelector('.number')) {
                    document.querySelector(`#navigation-bar-${pNum}`).querySelector('.number').innerHTML = totalAttempted;
                }
            }



            if (document.querySelectorAll(`.question-palette__part`).length > 0) {
                document.querySelectorAll(`.question-palette__part`).forEach((p) => {
                    let total = p.querySelectorAll(`.question-palette__item`).length;
                    let checked = p.querySelectorAll(`.question-palette__item.-checked`).length;
                    if ($('.js-attempt-only-reading').length > 0 || $('.js-attempt-only-listening').length > 0 || $('.js-attempt-only-writing').length > 0) {
                        if (total === checked) {
                            p.classList.add('-finished');
                        } else {
                            p.classList.remove('-finished');
                        }
                    }
                });

            }

        }

    });

    $(document).on('click', '#js-btn-previous', function (event) {
        if ($(`.question-palette__item.is-selected`).length > 0) {
            let cNum = $(`.question-palette__item.is-selected`).data('num-c');

            let startNum    = $(`.question-palette__item.is-selected`).data('start-num');
            let endNum      = $(`.question-palette__item.is-selected`).data('end-num');
            $(`.question-palette__item[data-end-num="${(+endNum - 1)}"]`).click();
        }
    });

    $(document).on('click', '#js-btn-next', function (event) {
        if ($(`.question-palette__item.is-selected`).length > 0) {
            // get current selected number
            let cNum        = $(`.question-palette__item.is-selected`).data('num-c');
            let pNum        = $(`.question-palette__item.is-selected`).data('p');
            let startNum    = $(`.question-palette__item.is-selected`).data('start-num');
            let endNum      = $(`.question-palette__item.is-selected`).data('end-num');
            // get next selected num and tigger it
            $(`.question-palette__item[data-start-num="${(+endNum + 1)}"]`).click();
            console.log($(`.question-palette__item[data-start-num="${(+endNum + 1)}"]`));

        }
    });

    window.countWords = (textAreaElm) => {
        var count = textAreaElm.val();
        count = count.match(/\S+/g);
        var words = count ? count.length : 0;
        var index = textAreaElm.data('questionItem') - 1;
        if (words == 0) {
            $('.question-palette__part').eq(index).removeClass('-finished');
        }
        $(textAreaElm).closest('.writing-box__answer-wrapper').find('.writing-box__words-num').text(words);
    }


    window.CountDownTimer = function (duration, duration_default, granularity) {
        this.duration = duration;
        this.granularity = granularity || 1000;
        this.tickFtns = [];
        this.running = false;
        this.pending = false;
        this.diff = parseInt(duration);
        this.duration_default = duration_default;
    }

    window.CountDownTimer.prototype.start = function () {
        if (this.running) {
            return;
        }

        this.running = true;
        var start = Date.now(),
            that = this,
            diff, obj;

        (function timer() {
            if (that.pending) {
                return;
            }
            var obj_default = window.CountDownTimer.parse(that.duration_default);
            if (that.duration_default == 0) {
                diff = parseInt(that.duration) + (((Date.now() - start) / 1000) | 0);
                if (diff >= 0) {
                    setTimeout(timer, that.granularity);
                }
                obj = window.CountDownTimer.parse(diff);
                var time_over = that.duration_default + diff;
                that.diff = window.CountDownTimer.parse(time_over);
                if(document.querySelector('#time-clock[data-current-time]')){
                    document.querySelector('#time-clock[data-current-time]').dataset.currentTime = time_over;
                    remainingTime = time_over;
                    window.rtime = time_over;
                }
                
            } else {
                diff = that.duration - (((Date.now() - start) / 1000) | 0);
                if (diff > 0) {
                    setTimeout(timer, that.granularity);
                    if(document.querySelector('#time-clock[data-current-time]')){
                        document.querySelector('#time-clock[data-current-time]').dataset.currentTime = diff;
                        remainingTime = diff;
                        window.rtime = diff;
                    }
                    
                } else {
                    diff = 0;
                    that.running = false;
                    if(document.querySelector('#time-clock[data-current-time]')){
                        document.querySelector('#time-clock[data-current-time]').dataset.currentTime = 0;
                        remainingTime = 0;
                        window.rtime = 0;
                    }
                }
                obj = window.CountDownTimer.parse(diff);
                var time_over = that.duration_default - diff;
                that.diff = window.CountDownTimer.parse(time_over);
            }

            that.tickFtns.forEach(function (ftn) {
                ftn.call(this, obj.minutes, obj.seconds, obj_default.minutes);
            }, that);
        }());
    };

    window.CountDownTimer.prototype.onTick = function (ftn) {
        if (typeof ftn === 'function') {
            this.tickFtns.push(ftn);
        }
        return this;
    };

    window.CountDownTimer.prototype.expired = function () {
        return !this.running;
    };

    window.CountDownTimer.prototype.timecurrent = function () {
        return this.diff;
    };
    window.CountDownTimer.prototype.pendingTime = function () {
        this.pending = true;
    }
    window.CountDownTimer.parse = function (seconds) {
        return {
            'minutes': (seconds / 60) | 0,
            'seconds': (seconds % 60) | 0
        };
    };


    function formatClockTime(minutes, seconds, minutes_default) {
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? seconds : seconds;
        var timeValue = minutes >= 1 ? minutes : seconds;
        var timeText = minutes >= 1 ? 'minutes remaining' : 'seconds remaining';
        if (minutes < 1) {
            $('.realtest-header').addClass('time-up');
            
        }
        if (elmDisplay) {
            if (parseInt(minutes) < parseInt(minutes_default) && parseInt(minutes) >= 1) {
                if (parseInt(seconds) == 0 && parseInt(minutes) == 1) {
                    timeValue = "01";
                }
                else {
                    timeValue = parseInt(minutes) + 1;
                    if (parseInt(minutes) < 9) {
                        timeValue = "0" + timeValue;
                    }
                }
            }
            elmDisplay.innerHTML = '<span class="realtest-header__time-val">' + timeValue + '</span>' + '<span class="realtest-header__time-text">' + timeText + '</span>';
            var currentTime = minutes*60 + seconds;
            jQuery('[data-current-time]').data('current-time',currentTime);
            console.log(currentTime);
        }
    }

    window.timeEndReading = function () {
        if (this.expired()) {
            var mode = 'practice_test';
            $('body').addClass('-test_time-up');
            if (mode == 'practice_test') {
                if ($('.question-palette__item.-checked').length) {
                    $('#modal-time-up').modal('show');
                } else {
                    $('#modal-time-up-no-taketest').modal('show');
                }
            } else if (mode == 'simulation_test') {
                if ($('.question-palette__item.-checked').length) {
                    //Drupal.postDataTest();
                } else {
                    $('#modal-time-up-no-taketest').modal('show');
                }
            } else {
                //Drupal.postDataTest()
            }
        }
    };



    // End countdown timer prototype
    // Run time clock.
    window.runTimeClock = function (timeEndCallback) {
        var elmDisplay = document.querySelector('#time-clock');
        if(jQuery('[data-timer="1"]').length == 1){
            console.log('[timer]: enabled');
            if (elmDisplay) {
                var timeDuration = elmDisplay.dataset.time,
                    timeDurationDefault = elmDisplay.dataset.durationDefault;

                timer = new window.CountDownTimer(timeDuration, timeDurationDefault);
                var timeObj = window.CountDownTimer.parse(timeDuration);
                formatClockTime(timeObj.minutes, timeObj.seconds, timeObj.minutes);
                timer.onTick(formatClockTime);
                // Provided custom callback function will be called at timer end.
                if (typeof timeEndCallback === 'function') {
                    timer.onTick(timeEndCallback);
                } else {
                    timer.onTick(timeEnd);
                }
                timer.onTick(timeTakeTest);
                timer.start();
            }
        }

        function timeEnd() {
            if (this.expired()) {
                // showTimeIsUpModal();
            }
        }

        function timeEndAlertModal() {
            if (this.expired()) {
                console.log('Time is up! please write your function to submit form here.')
            }
        }

        // Save time current to local storage.
        function timeTakeTest() {
            var timecurrent = this.timecurrent();
            var minutes = timecurrent.minutes < 10 ? "0" + timecurrent.minutes : timecurrent.minutes;
            var seconds = timecurrent.seconds < 10 ? "0" + timecurrent.seconds : timecurrent.seconds;
            if (timecurrent === undefined) {
                return;
            }
            /*
            var taketest_string = window.getCookie('taketest');
            if (!taketest_string) {
              return;
            }
            */
            //localStorage.setItem("practice_" + quiz_id + '_timecurrent', minutes + ':' + seconds);
        }

        // Set flag default for time clock.
        function formatClockTime(minutes, seconds, minutes_default) {
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? seconds : seconds;
            var timeValue = minutes >= 1 ? minutes : seconds;
            var timeText = minutes >= 1 ? 'minutes remaining' : 'seconds remaining';
            if (minutes < 1) {
                $('.realtest-header').addClass('time-up');
            }
            if (elmDisplay) {
                if (parseInt(minutes) < parseInt(minutes_default) && parseInt(minutes) >= 1) {
                    if (parseInt(seconds) == 0 && parseInt(minutes) == 1) {
                        timeValue = "01";
                    }
                    else {
                        timeValue = parseInt(minutes) + 1;
                        if (parseInt(minutes) < 9) {
                            timeValue = "0" + timeValue;
                        }
                    }
                }
                elmDisplay.innerHTML = '<span class="realtest-header__time-val">' + timeValue + '</span>' + '<span class="realtest-header__time-text">' + timeText + '</span>';
            }
        }

        function formatEndTimeAlert(minutes, seconds) {
            var countDownText = document.getElementById("js-countdown-text");
            var seconds = seconds;
            timeDisplayElm.textContent = seconds;
            countDownText.textContent = seconds <= 1 ? "sec" : "secs";
        }

        function showTimeIsUpModal() {
            $("#modal-time-up").modal({ backdrop: 'static', keyboard: false });
            timeDisplayElm = document.querySelector('#js-countdown-number');
            if (timeDisplayElm) {
                var timeDuration = timeDisplayElm.dataset.time,
                    timer = new window.CountDownTimer(timeDuration),
                    timeObj = window.CountDownTimer.parse(timeDuration);
                formatEndTimeAlert(timeObj.minutes, timeObj.seconds);
                timer.onTick(formatEndTimeAlert);
                timer.onTick(timeEndAlertModal);
                timer.start();
            }
        }
    }
    window.pendingTimeClock = function () {
        // Stop time clock.
        timer.pendingTime();
    }
    // End function countdown timer.
    // --------------------------------



    // review questions answers
    $(document).on('click', '.js-attempt-only-reading .realtest-header__bt-review', function () {
        var target = $(this).data('target');
        var confirm = window.updateAnsweredQuestions();
        if (confirm !== null) {
            $(target).modal('show');
        }
    });

    $(document).on('click', '.js-attempt-only-listening .realtest-header__bt-review', function () {
        var target = $(this).data('target');
        var confirm = window.updateAnsweredQuestions();
        if (confirm !== null) {
            $(target).modal('show');
        }
    });

    const getChar = (num) => {
        const Char = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        if(Char[num] !== undefined){
            return Char[num];
        } else {
            return num;
        }
    };

    window.updateAnsweredQuestions = function () {
        if ($('.question-palette__item').length) {
            var answers = {}
            var totalAnswered = 0;
            var count_questions = $('.question-palette__item').length;
            var count_questions_not_answered = $('.question-palette__item:not(.-checked)').length;
            var count_questions_answered = count_questions - count_questions_not_answered;
            // Get all answered questions.
            $('.question-palette__item').each(function (index, el) {
                // $('.modal-review-test__table .result-table').find('[data-num="' + $(this).data('num') + '"] em').text(answer);
                var qNum = $(el).data('num');
                var pNum = $(el).data('p');

                if ($(`.question__input[data-num="${qNum}"][data-part="${pNum}"]`).length > 0) {


                    let qInput = $(`.question__input[data-num="${qNum}"][data-part="${pNum}"]`);
                    let q_num = qInput.data('num');
                    let p_num = qInput.data('part');
                    let input_type = qInput.data('input_type');
                    let q_type = qInput.data('q_type');
                    let q_total = qInput.data('q_total');



                    if (input_type === "select") {

                        if (qInput.val() !== "") {
                            answers[q_num] = {
                                qNum: q_num,
                                pNum: p_num,
                                input_type: input_type,
                                q_type: q_type,
                                answer: qInput.val(),
                                class: '-checked',
                                total: q_total,
                                answers: []
                            };

                            $(`.question-palette__item[data-num="${qNum}"][data-p="${pNum}"]`).addClass('-checked');
                            $(`.result-table .result-table__col[data-num="${qNum}"][data-p="${pNum}"] em`).text(qInput.val().trim());
                        } else {
                            answers[qNum] = {
                                qNum: qNum,
                                pNum: pNum,
                                input_type: input_type,
                                q_type: q_type,
                                answer: '',
                                class: '-unchecked',
                                total: q_total,
                                answers: []
                            };
                            $(`.question-palette__item[data-num="${qNum}"][data-p="${pNum}"]`).removeClass('-checked');
                            $(`.result-table .result-table__col[data-num="${qNum}"][data-p="${pNum}"] em`).text("");
                        }
                    } else if (input_type === "fillup") {
                        if (qInput.val() !== "" || qInput.val().trim().length > 0) {
                            answers[q_num] = {
                                qNum: q_num,
                                pNum: p_num,
                                input_type: input_type,
                                q_type: q_type,
                                answer: qInput.val(),
                                class: '-checked',
                                total: q_total,
                                answers: []
                            };

                            $(`.question-palette__item[data-num="${qNum}"][data-p="${pNum}"]`).addClass('-checked');
                            $(`.result-table .result-table__col[data-num="${qNum}"][data-p="${pNum}"] em`).text(qInput.val().trim());
                        } else {
                            answers[qNum] = {
                                qNum: qNum,
                                pNum: pNum,
                                input_type: input_type,
                                q_type: q_type,
                                answer: '',
                                class: '-unchecked',
                                total: q_total,
                                answers: []
                            };
                            $(`.question-palette__item[data-num="${qNum}"][data-p="${pNum}"]`).removeClass('-checked');
                            $(`.result-table .result-table__col[data-num="${qNum}"][data-p="${pNum}"] em`).text("");
                        }

                    } else if (input_type === "radio") {
                        
                        if ($(`[name="q-${qNum}"]:checked`).length > 0) {
                            answers[qNum] = {
                                qNum: qNum,
                                pNum: pNum,
                                input_type: input_type,
                                q_type: q_type,
                                answer: $(`[name="q-${qNum}"]:checked`).val(),
                                class: '-checked',
                                total: q_total,
                                answers: []
                            };

                            let ans = $(`[name="q-${qNum}"]:checked`).val();
                            let char = getChar(+ans - 1);

                            $(`.question-palette__item[data-num="${qNum}"][data-p="${pNum}"]`).addClass('-checked');
                            $(`.result-table .result-table__col[data-num="${qNum}"][data-p="${pNum}"] em`).text(char);
                        } else {
                            answers[qNum] = {
                                qNum: qNum,
                                pNum: pNum,
                                input_type: input_type,
                                q_type: q_type,
                                answer: '',
                                class: '-unchecked',
                                total: q_total,
                                answers: []
                            };
                            $(`.question-palette__item[data-num="${qNum}"][data-p="${pNum}"]`).removeClass('-checked');
                            $(`.result-table .result-table__col[data-num="${qNum}"][data-p="${pNum}"] em`).text("");
                        }

                    } else if (input_type === "checkbox" ) {

                        if ($(`[name="q-${qNum}"]:checked`).length > 0) {
                            let checkedOptions = [];
                            $.each($(`[name="q-${qNum}"]:checked`), function(index, value) {
                                checkedOptions.push(value.value);

                            });

                            console.log(checkedOptions);

                            var transformedArray = $.map(checkedOptions, function(value) {
                                return getChar(+value - 1);
                            });
                            let char = transformedArray.join(',');

                            if(checkedOptions.length > 0){
                                $(`.question-palette__item[data-num="${qNum}"][data-p="${pNum}"]`).addClass('-checked');
                                $(`.result-table .result-table__col[data-num="${qNum}"][data-p="${pNum}"] em`).text(char);
                                answers[qNum] = {
                                    qNum: qNum,
                                    pNum: pNum,
                                    input_type: input_type,
                                    q_type: q_type,
                                    answer: '',
                                    answers: checkedOptions,
                                    class: '-checked',
                                    total: q_total
                                };
                            } else {
                                $(`.question-palette__item[data-num="${qNum}"][data-p="${pNum}"]`).removeClass('-checked');
                                $(`.result-table .result-table__col[data-num="${qNum}"][data-p="${pNum}"] em`).text('');
                                answers[qNum] = {
                                    qNum: qNum,
                                    pNum: pNum,
                                    input_type: input_type,
                                    q_type: q_type,
                                    answer: '',
                                    answers: [],
                                    class: '-unchecked',
                                    total: q_total
                                };
                            }   
                            
                            
                            

                        } else {
                            answers[qNum] = {
                                qNum: qNum,
                                pNum: pNum,
                                input_type: input_type,
                                q_type: q_type,
                                answer: '',
                                answers: [],
                                class: '-unchecked',
                                total: q_total
                            };



                            $(`.question-palette__item[data-num="${qNum}"][data-p="${pNum}"]`).removeClass('-checked');
                            $(`.result-table .result-table__col[data-num="${qNum}"][data-p="${pNum}"] em`).text("");
                        }
                    }

                }
            });

            

            return answers;



        }

    };
    // for writing auto save
    window.updateEssayAnswered = function () {
        let answers = {};
        if ($('.question-palette__item').length){
            
            var count_questions = $('.question-palette__item').length;
            if( count_questions > 0 ) {
                $('.question-palette__item').each(function (index, el) {
                    var qNum = $(el).data('num');
                    var pNum = $(el).data('p');

                    if ($(`.question__input[data-num="${qNum}"][data-part="${pNum}"]`).length > 0) {

                        let qInput = $(`.question__input[data-num="${qNum}"][data-part="${pNum}"]`);
                        let q_num   = qInput.data('num');
                        let p_num   = qInput.data('part');
                        let pId     = qInput.data('section-id');
                        let pOrder  = qInput.data('section-order');
                        let input_type = qInput.data('input_type');
                        let q_type = qInput.data('q_type');
                        if (input_type === "textarea") {
                            if (qInput.val() !== ""){
                                answers[p_num] = {
                                    qNum: q_num,
                                    pNum: p_num,
                                    id: pId,
                                    order: pOrder,
                                    input_type: input_type,
                                    q_type: q_type,
                                    answer: qInput.val(),
                                    class: '-checked'
                                    
                                };
                            } else {
                                answers[p_num] = {
                                    qNum: q_num,
                                    pNum: p_num,
                                    id: pId,
                                    order: pOrder,
                                    input_type: input_type,
                                    q_type: q_type,
                                    answer: '',
                                    class: '-unchecked'
                                    
                                };
                            }
                        }

                    }

                });
            }


        }
        return answers;
    };

    // load answers
    window.loadAnswers = function(answers = {}) {
        Object.entries(answers).forEach(([key, value]) => {
            const { } = value;
        });
    };

    // submit popup modal display : reading
    $(document).on('click', '.js-attempt-only-reading .realtest-header__bt-submit ', function () {
        var time_duration_default = $('#time-clock').data('duration-default');
        if (time_duration_default == 0) {
            if ($('.question-palette__item.-checked').length) {
                $('#modal-submit-test').modal('show');
            } else {
                $('#modal-do-not-work-lr').modal('show');
            }
        } else {
            if ($('.question-palette__item.-checked').length) {
                $('#modal-submit-test').modal('show');
            } else {
                $('#modal-do-not-work-lr').modal('show');
            }
        }
    });

    // submit popup modal display : listening
    $(document).on('click', '.js-attempt-only-listening .realtest-header__bt-submit ', function () {
        var time_duration_default = $('#time-clock').data('duration-default');
        if (time_duration_default == 0) {
            if ($('.question-palette__item.-checked').length) {
                $('#modal-submit-test').modal('show');
            } else {
                $('#modal-do-not-work-lr').modal('show');
            }
        } else {
            if ($('.question-palette__item.-checked').length) {
                $('#modal-submit-test').modal('show');
            } else {
                $('#modal-do-not-work-lr').modal('show');
            }
        }
    });


    // submit popup modal display : writing 
    $(document).on('click', '.js-attempt-only-writing .realtest-header__bt-submit ', function () {
        var time_duration_default = $('#time-clock').data('duration-default');
        let answerGiven = false;
        if ($('.question-palette__item').length){
            
            var count_questions = $('.question-palette__item').length;
            if( count_questions > 0 ) {
                $('.question-palette__item').each(function (index, el) {
                    var qNum = $(el).data('num');
                    var pNum = $(el).data('p');

                    if ($(`.question__input[data-num="${qNum}"][data-part="${pNum}"]`).length > 0) {

                        let qInput = $(`.question__input[data-num="${qNum}"][data-part="${pNum}"]`);
                        let q_num = qInput.data('num');
                        let p_num = qInput.data('part');
                        let input_type = qInput.data('input_type');
                        let q_type = qInput.data('q_type');
                        if (input_type === "textarea") {
                            if (qInput.val() !== ""){
                                answerGiven = true;
                            }
                        }

                    }

                });
            }


        }


        
        if (time_duration_default == 0) {
            if (answerGiven) {
                $('#modal-submit-essay').modal('show');
            } else {
                $('modal-not-taketest').modal('show');
            }
        } else {
            if (answerGiven) {
                $('#modal-submit-essay').modal('show');
            } else {
                $('#modal-not-taketest').modal('show');
            }
        }
    });



    // on timesup - answer given submit btn
    $(document).on('click','.-btn-submit-test', function(event){
        let btn = $(this);
        window.submitQuiz();
    });


    // main submit
    window.submitQuiz = () => {
        let answers = JSON.stringify(window.updateAnsweredQuestions());
        let diff    = document.querySelector('#time-clock[data-current-time]')? document.querySelector('#time-clock[data-current-time]').dataset.currentTime : 0;
        let time    = document.querySelector('#time-clock[data-time]')? document.querySelector('#time-clock[data-time]').dataset.time : 0;
        let json    = JSON.parse(jQuery('#session-details[type="application/json"]').text());
        let quiz    = JSON.parse(jQuery('#quiz-json[type="application/json"]').text());
        let audioTime = 0;

        if(jQuery('#take-test__player').length > 0){
            audioTime = jQuery('#take-test__player')[0].currentTime;
        }

        let autosave = true;
        console.info('[FINISH] : submitting');
        let response = JSON.stringify(window.updateAnsweredQuestions());
        if( json.category == "ielts-writing" ) {
            response = JSON.stringify(window.updateEssayAnswered());
        }
        if(autosave){
            clearInterval(saveInterval);
            window.saveQuiz({
                time: time,
                audio: +audioTime,
                diff: window.rtime? window.rtime : 0,
                json: json,
                quiz: JSON.stringify(quiz),
                answers: response,
                finish: 1 
            });
        }

    };

    // auto saving feature :- [major]
    window.runAutoSave = () => {
        // if ($('.question-palette__item.-checked').length > 0){
        
            if(1 == 1){
                
                let diff    = document.querySelector('#time-clock[data-current-time]')? document.querySelector('#time-clock[data-current-time]').dataset.currentTime : 0;
                let time    = document.querySelector('#time-clock[data-time]')? document.querySelector('#time-clock[data-time]').dataset.time : 0;
                let json    = JSON.parse(jQuery('#session-details[type="application/json"]').text());
                let quiz    = JSON.parse(jQuery('#quiz-json[type="application/json"]').text());
                




                let autosave = true;
                
                if(json){
                    
                    if(json.category == "ielts-writing"){
                        if(autosave){
                            saveInterval = setInterval(() => {
                                window.saveQuiz({
                                    time: time,
                                    audio: 0,
                                    diff: window.rtime? window.rtime : 0,
                                    json: json,
                                    answers: JSON.stringify(window.updateEssayAnswered()),
                                    finish: 3
                                });
        
                                if(!autosave){
                                    clearInterval(saveInterval);
                                }
                            },20000); // each 20s
                        }
                    } else {
                        if(autosave){

                            saveInterval = setInterval(() => {
                                let audioTime = 0;
                                if(jQuery('#take-test__player').length > 0){
                                    audioTime = jQuery('#take-test__player')[0].currentTime;
                                }
                                window.saveQuiz({
                                    time: time,
                                    audio: +audioTime,
                                    diff: window.rtime? window.rtime : 0,
                                    json: json,
                                    answers: JSON.stringify(window.updateAnsweredQuestions()),
                                    finish: 3
                                });
        
                                if(!autosave){
                                    clearInterval(saveInterval);
                                }
                            },20000); // each 20s
                        }
                    }
                }

                

            }
        // }
    };

    window.saveQuiz = (data = {}) => {
        let tmpTitle = window.document.title;
        saveAjax = jQuery.ajax({
            type: "POST",
            url: window.site_url +'/wp-admin/admin-ajax.php',
            data: {
                "action": "proqyz-save-quiz-progress",
                "data": data
            },
            beforeSend: function () {
                if (saveAjax != null) {
                    saveAjax.abort();
                }
                console.log('[RES]: Saving...');
                window.document.title = "Saving...";
                if(data.finish == 1){
                    jQuery('.modal .proqyz_btn').attr('disabled',true);
                }

            },
            success: function (response) {
                jQuery('.modal .proqyz_btn').attr('disabled',false);
                if (response.success) {
                    if (response.redirect) {
                        jQuery('.modal').modal('hide');
                        jQuery(document).on('click','.-btn-redirect-result', function(){
                            window.location.href = response.url;
                        });
                        jQuery('#modal-finish').modal('show');
                        window.location.href = response.url;
                    }
                }
            },
            error: function (err) {
                jQuery('.modal .proqyz_btn').attr('disabled',false);
                if (err.status == 404) {
                    console.warn("404 Auth location !");
                } else if (err.status == 0) {
                    console.warn("Network Error reason [abort]");
                } else {
                    console.warn(err.message);
                }
            }
        }).always(function() {
            window.document.title = tmpTitle;
        });
    };

    // retake btn if not attempt
    $(document).on('click', '.-btn-retake', function(){
        let json    = JSON.parse(jQuery('#session-details[type="application/json"]').text());
        jQuery.ajax({
            type: "POST",
            url: window.site_url +'/wp-admin/admin-ajax.php',
            data: {
                "action": "proqyz-quiz-progress-restart",
                "data"  : {
                    "json" : json
                }
            },
            beforeSend: function () {
                if (saveAjax != null) {
                    saveAjax.abort();
                }
                console.log('[RES]: Restarting...');
            },
            success: function (response) {
                if(response.success) {
                    if(document.querySelector('#time-clock')){
                        document.querySelector('#time-clock').dataset.currentTime = +document.querySelector('#time-clock').dataset.durationDefault; 
                        document.querySelector('#time-clock').dataset.time = +document.querySelector('#time-clock').dataset.durationDefault; 
                        if(jQuery('#take-test__player').length > 0){
                            jQuery('#take-test__player')[0].currentTime = 0;
                            jQuery('#take-test__player')[0].play();
                        }
                        console.log('[TIME]: ',document.querySelector('#time-clock').dataset.currentTime);
                    }

                    window.runTimeClock(window.timeEndReading);
                    window.runAutoSave();
                    jQuery('#modal-resume').modal('hide');
                    $('body').removeClass('-test_time-up');
                    $('.realtest-header').removeClass('time-up');
                    $('.modal').modal('hide');
                }
            },
            error: function (err) {
                if (err.status == 404) {
                    console.warn("404 Auth location !");
                } else if (err.status == 0) {
                    alert("Network Error");
                } else {
                    console.warn(err.message);
                }
            }
        });
    });


    function runTestPanelNiceScroll() {

        var windowWidth = $(window).width();
        var elements = $('.test-panel, .test-contents');
        if (windowWidth > 1024) {
            elements.niceScroll({
                autohidemode: false,
                cursorborderradius: 6,
                cursorwidth: "8px",
                cursorcolor: "#dfdfdf",
                horizrailenabled: false,
            });
            initNiceScroll = true;
        } else {
            elements.each(function (index, el) {
                $(el).getNiceScroll().remove();
                initNiceScroll = false;
            });
        }
    }

    window.addEventListener('resize',function(){
        $(".test-panel, .test-contents").getNiceScroll().resize();
    });

    window.splitReadingTestScreen = () => {
        var spliter;
        var panelDirection;
        if ($('.gutter.gutter-horizontal').length) {
            return;
        }
    
        function initializeSpliter() {
            var windowWidth = $(window).width();
            panelDirection = (windowWidth < 768) ? 'vertical' : 'horizontal';
    
            if ($('#split-one').length && $('#split-two').length) {
                spliter = Split(['#split-one', '#split-two'], {
                gutterSize: 4,
                sizes: [50, 50],
                direction: panelDirection,
                onDrag: onDragFunction
                });
            }
        }
    
        function onDragFunction() {
            $('.test-panel, .test-contents').getNiceScroll().resize();
        }
    
        function destroyAndRecreateSpliter() {
            if (spliter) {
                spliter.destroy();
                initializeSpliter();
            }
        }
    
        function handleWindowResize() {
            var windowWidth = $(window).width();
            var panelDirectionResize = (windowWidth < 768) ? 'vertical' : 'horizontal';
        
            if (panelDirectionResize !== panelDirection) {
                destroyAndRecreateSpliter();
                panelDirection = panelDirectionResize;
            }
        }
    
        $(window).resize(handleWindowResize);
    
        initializeSpliter();
    };


    $(document).on('click','#btn-continue', function(){
        jQuery('#modal-resume').modal('hide');
        if(jQuery('#take-test__player').length > 0){
            jQuery('#take-test__player')[0].currentTime = jQuery('#take-test__player')[0].dataset.audioTime;
            jQuery('#take-test__player')[0].play();
        }
        window.runTimeClock(window.timeEndReading);
        window.runTestPanelNiceScroll();
        window.runAutoSave();
    });

    $(document).on('click','#btn-restart', function(){
        let json    = JSON.parse(jQuery('#session-details[type="application/json"]').text());
        jQuery.ajax({
            type: "POST",
            url: window.site_url +'/wp-admin/admin-ajax.php',
            data: {
                "action": "proqyz-quiz-progress-restart",
                "data"  : {
                    "json" : json
                }
            },
            beforeSend: function () {
                if (saveAjax != null) {
                    saveAjax.abort();
                }
                console.log('[RES]: Restarting...');
            },
            success: function (response) {
                if(response.success) {
                    if(document.querySelector('#time-clock')){
                        document.querySelector('#time-clock').dataset.currentTime = +document.querySelector('#time-clock').dataset.durationDefault; 
                        document.querySelector('#time-clock').dataset.time = +document.querySelector('#time-clock').dataset.durationDefault; 
                        if(jQuery('#take-test__player').length > 0){
                            jQuery('#take-test__player')[0].currentTime = 0;
                            jQuery('#take-test__player')[0].play();
                        }
                        console.log('[TIME]: ',document.querySelector('#time-clock').dataset.currentTime);
                    }

                    window.runTimeClock(window.timeEndReading);
                    window.runAutoSave();
                    jQuery('#modal-resume').modal('hide');
                }
            },
            error: function (err) {
                if (err.status == 404) {
                    console.warn("404 Auth location !");
                } else if (err.status == 0) {
                    alert("Network Error");
                } else {
                    console.warn(err.message);
                }
            }
        });
    });

    $(document).on('click', '#show-result-modal', function(){
        $('#result-modal').modal('show');
        setTimeout(() => {
            $(".test-panel, .test-contents").getNiceScroll().resize();
        },555);
        
    });

    $(document).on('click','.take-test__play-btn_click', function(){
        $(this).parent().css('display','none');
        $('body').removeClass('disabled-controls');
        if(jQuery('#take-test__player').length > 0){
            jQuery('#take-test__player')[0].currentTime = 0;
            jQuery('#take-test__player')[0].play();
        }
        window.runTimeClock(window.timeEndReading);
        window.runTestPanelNiceScroll();
        window.runAutoSave();
    });

    // locate question 
    $(document).on('click','.locate-explain', function(){
        let qix = $(this).data('q');
        if(document.querySelector(`[data-no="Q${qix}"]`)){
            document.querySelector(`[data-no="Q${qix}"]`).scrollIntoView({
                behavior: "smooth",
                block: "center",
                inline: "start"
            });

            document.querySelector(`[data-no="Q${qix}"]`).classList.add('blink');
            document.querySelector(`[data-no="Q${qix}"]`).addEventListener('mouseover', () => {
                document.querySelector(`[data-no="Q${qix}"]`).classList.remove('blink');
            });
            setInterval(() => {
                document.querySelector(`[data-no="Q${qix}"]`).classList.remove('blink');
            }, 3000);
        }

    });
    
    $(document).on('click',".explanation-click", function () {
        var currentPosition = $("#split-two").scrollTop();
        setTimeout(function () {
            if ($(window).width() > 768) {
                $(".test-panel, .test-contents").getNiceScroll().resize();
                $("#split-two").scrollTop(currentPosition);
            }
        }, 500);
    });

    // report-question btn events
    $(document).on('click','[data-type="question-report"]', function(){
        let itemNo = $(this).data('q_item');
        let select = $('#report-mistake select[name="question"]');
            select.val(itemNo);
            $('.selectpicker').selectpicker('refresh');
            $('#report-question').modal('show');
        

    });
    // 2024 - report questopn
    $(document).on('submit','#report-mistake', function(event){
        event.preventDefault();
        let select = $('#report-mistake select[name="question"]'); // Adjust selector as needed
        let selectedOption = select.find(':selected'); // Get the selected <option>
        let selectedValue = selectedOption.val(); // Get the value of the selected <option>
        let selectedDataset = selectedOption.data('id'); // Get 
        let postId = selectedOption.data('sid');
        // let data_box = $(`[data-type="question-block"][data-q_id="${selectedDataset}"]`);
        // report message
        let message = $('#report-mistake #report-message');
        let closeBtn    = $('#report-mistake [data-dismiss="modal"]');
        let submitBtn   = $('#report-mistake [type="submit"]');

        try{
            
            // result_url - global
            // result_id - global
            
            /* if (data_box.length === 0) {
                alert('No matching element found!');
                return;
            } */
            // Thank you for your feedback, we will investigate and resolve the issue
        
            
            // Use html2canvas to take a screenshot of the element
            let formData = new FormData();
                formData.append('action', 'proqyz-save-question-report');
                //formData.append('image', screenshotData);
                formData.append('question_id', selectedDataset);
                // formData.append('result_url', resultUrl !== undefined? resultUrl : '');
                // formData.append('result_id', resultId !== undefined? resultId : '');
                formData.append('quiz_id', quizId);
                formData.append('post_id', postId);
                formData.append('category', quizCategory);
                formData.append('question_number', selectedValue);
                formData.append('message', message.val());
                

                // AJAX call to send the image
                $.ajax({
                    type: 'POST',
                    url: window.site_url +'/wp-admin/admin-ajax.php',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false, // Add this line
                    beforeSend : function(){
                        closeBtn.attr('disabled',true);
                        submitBtn.attr('disabled',true);
                        submitBtn.html(`Submitting <i class="fa fa-spin fa-spinner"></i>`);
                    },
                    success: function(data){
                        
                        if(data.success){
                            $('#modal-report .report__feedback').html(`
                                <p style="text-align: center;padding-top: 50px;margin: 0 auto;"> 
                                    ${data.success?.message || 'Thankyou'}
                                </p>    
                            `);
                            $('#report-question').modal('hide');    
                            $('#modal-report').modal('show');
                            $('#report-mistake #report-message').val('');
                            select.val();
                            $('.selectpicker').selectpicker('refresh');
                        }

                        if(data.error) {
                            $('#modal-report .report__feedback').html(`
                                <p style="text-align: center;padding-top: 50px;margin: 0 auto;color: red;"> 
                                    ${data.error?.message || 'Invalid response'}
                                </p>    
                            `);
                            $('#report-question').modal('hide');    
                            $('#modal-report').modal('show');
                            
                        }

                        closeBtn.attr('disabled',false);
                        submitBtn.attr('disabled',false);
                        submitBtn.html(`Submit`);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        closeBtn.attr('disabled',false);
                        submitBtn.attr('disabled',false);
                        $('#modal-report .report__feedback').html(`
                            <p style="text-align: center;padding-top: 50px;margin: 0 auto;color: red;"> 
                                ${error?.message || 'Invalid response'}
                            </p>    
                        `);
                        $('#report-question').modal('hide');    
                        $('#modal-report').modal('show');
                        submitBtn.html(`Submit`);
                    }

                });
            
            /*
            html2canvas(data_box[0]).then((canvas) => {
                // Convert the canvas to a data URL
                let screenshotData = canvas.toDataURL('image/png');
        
                // Send the screenshot to the server
                let formData = new FormData();
                formData.append('action', 'proqyz-save-question-report');
                formData.append('image', screenshotData);
                formData.append('question_id', selectedDataset);
                formData.append('result_url', resultUrl);
                formData.append('result_id', resultId);
                formData.append('quiz_id', quizId);
                formData.append('post_id', postId);
                formData.append('category', quizCategory);
                formData.append('question_number', selectedValue);
                formData.append('message', message.val());
                

                // AJAX call to send the image
                $.ajax({
                    type: 'POST',
                    url: window.site_url +'/wp-admin/admin-ajax.php',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false, // Add this line
                    beforeSend : function(){

                    },
                    success: function(data){
                        
                        if(data.success){
                            $('#modal-report .report__feedback').html(`
                                <p style="text-align: center;padding-top: 50px;margin: 0 auto;"> 
                                    ${data.success?.message || 'Thankyou'}
                                </p>    
                            `);
                            $('#report-question').modal('hide');    
                            $('#modal-report').modal('show');
                        }

                        if(data.error) {
                            $('#modal-report .report__feedback').html(`
                                <p style="text-align: center;padding-top: 50px;margin: 0 auto;color: red;"> 
                                    ${data.error?.message || 'Invalid response'}
                                </p>    
                            `);
                            $('#report-question').modal('hide');    
                            $('#modal-report').modal('show');
                        }

                        closeBtn.attr('disabled',false);
                        submitBtn.attr('disabled',false);
                        submitBtn.html(`Submit`);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        closeBtn.attr('disabled',false);
                        submitBtn.attr('disabled',false);
                        $('#modal-report .report__feedback').html(`
                            <p style="text-align: center;padding-top: 50px;margin: 0 auto;color: red;"> 
                                ${error?.message || 'Invalid response'}
                            </p>    
                        `);
                        $('#report-question').modal('hide');    
                        $('#modal-report').modal('show');
                        submitBtn.html(`Submit`);
                    }

                });
            });
            */
            
        } catch(er) {
            closeBtn.attr('disabled',false);
            submitBtn.attr('disabled',false);
            $('#modal-report .report__feedback').html(`
                <p style="text-align: center;padding-top: 50px;margin: 0 auto;color: red;"> 
                    ${er.message}
                </p>    
            `);
            $('#report-question').modal('hide');    
            $('#modal-report').modal('show');
            submitBtn.html(`Submit`);

        }
    });

    $(document).on('change','[data-q_selector="question"]', function() {
        let select = $(this); // Adjust selector as needed
        let selectedOption = select.find(':selected'); // Get the selected <option>
        let selectedValue = selectedOption.val(); // Get the value of the selected <option>
        let selectedDataset = selectedOption.data('id'); // Get 
        let postId = selectedOption.data('sid');
        $(`.question-palette__item[data-num="${selectedValue}"]`).click();


    });

    $(document).on('click','.btn__report-question', function(){
        $('.selectpicker').selectpicker('refresh');
        $('#report-question').modal('show');
    });



    if(jQuery('#take-test__player').length > 0){
        
        window.listeningPlayer  = new Plyr('#take-test__player', {
            controls: +jQuery('#take-test__player').data('audio-controls') == 1? 
            ['play-large', 'rewind', 'play', 'fast-forward', 'current-time', 'progress', 'mute', 'volume'] :['mute','volume','current-time'],
            hideControls: true,
            settings: [],
            seekTime: 5,
            youtube: {
                noCookie: true,
            }
        });

        if(+jQuery('#take-test__player').data('audio-controls') != 1){
            if(document.querySelector('#take-test__player .plyr__controls')) {
                // document.querySelector('#take-test__player .plyr__controls').remove();
            }
        }

        $(document).on('click','.proqyz-lfh', function(){
            let time = $(this).data('time');
            console.log('time: ', time);
            
            if(document.querySelector('#take-test__player')){
                document.querySelector('#take-test__player').currentTime = time;
                document.querySelector('#take-test__player').play();
            }
        });
        
    }

    window.splitReadingTestScreen();
    window.runTestPanelNiceScroll = runTestPanelNiceScroll;
});


window.preload = () => {
    jQuery(document).ready(function ($) {


        var tooltipVisible = false;
        var noteInput = $('#js-note-content');
        var highlightTooltip = $('#highlight-box');
        var highlighterContent = $('#highlighter-contents');

        var NoteIds = [];
        var newAddedNote = 0;
        var notes = [];
        var noteSerialized = {};
        var initNiceScroll = false;

        // --------------------------------
        // NOTE/HIGHLIGHT FUNCTIONS
        function getQuizId() {
            return 'practice_test';
        }

        // (Notepad) Setup note - Black out the text show active note and highlight.
        function setupNoteApp() {
            // var quizID = getQuizId();
            // // let notes = [];
            // // Check if localstorage contains any data
            // const localData = localStorage.getItem('notes_' + quizID);
            // // Retrieve the list of notes from localstorage
            // if (localData) {
            //   notes = JSON.parse(localData);
            // }
            //
            // if (notes && notes.length > 0) {
            //   // Display the saved notes
            //   renderNotes();
            // }

            // Handle "input" event on the search input
            $('#note-search').on('input', function () {
                const searchTerm = $(this).val().trim().toLowerCase();
                if (searchTerm === '') {
                    // If the search term is empty, display the original notes
                    $('#search-results').empty();
                    $('#notes-container').show();
                    renderNotes();
                }
                else {
                    const filteredNotes = filterNotes(searchTerm);

                    // Render the filtered notes
                    renderSearchResults(filteredNotes);
                }
            });

            function filterNotes(searchTerm) {
                // Filter notes that contain the search term in either selectedText or
                // noteText
                return notes.filter((note) => {
                    const selectedText = note.selectedText.toLowerCase();
                    const noteText = note.noteText.toLowerCase();

                    return selectedText.includes(searchTerm) || noteText.includes(searchTerm);
                });
            }

            function renderSearchResults(searchResults) {
                const searchResultsContainer = $('#search-results');
                searchResultsContainer.empty();

                if (searchResults.length > 0) {
                    $.each(searchResults, function (index, note) {
                        const searchResultDiv = `
                <div class="notepad__item" data-note-id="${note.id}" data-note-part="${note.noteOfPart}"  data-ref-id="${note.noteRefId}">
                  <div class="notepad__item-title">${note.selectedText}</div>
                  <div class="notepad__item-content-wrap">
                    <div class="notepad__item-content">${note.noteText}</div>
                  </div>
                  <span class="notepad__item-more">
                    <span class="notepad__item-more-icon ioticon-more-vertical"></span>
                    <span class="notepad__more-card">
                      <span class="notepad__more-item-row -edit">Edit <span class="notepad__more-item-icon ioticon-edit"></span></span>
                      <span class="notepad__more-item-row">
                        Delete <span class="notepad__more-item-icon ioticon-trash-3 -delete"></span>
                        <span class="notepad__delete-confirm">Are you sure to delete this note?
                          <span class="notepad__text-confirm-wrap">
                            <span class="notepad__text-confirm -delete" data-id="${note.id}">Yes</span>
                            <span class="notepad__text-confirm -cancel">No</span>
                          </span>
                        </span>
                      </span>
                    </span>
                  </span>
                </div>
              `;

                        // Add search results with delete and cancel function
                        const searchResultElement = $(searchResultDiv);
                        searchResultsContainer.prepend(searchResultElement);
                    });
                    $('#notes-container').hide();
                }
                else {
                    const noResultsDiv = `<div class="notepad__no-results">No matching note found.</div>`;
                    searchResultsContainer.append(noResultsDiv);
                }
            }

            function deleteNote(noteId) {
                // Find a note by its unique ID.
                const noteIndex = notes.findIndex(note => note.id === noteId);
                if (noteIndex !== -1) {
                    // Remove a note from the notes array using the found index.
                    notes.splice(noteIndex, 1);

                    // Save the notes array to local storage.
                    // localStorage.setItem('notes_' + quizID, JSON.stringify(notes));

                    // Display the search results again.
                    const searchTerm = $('#note-search').val().trim().toLowerCase();
                    if (searchTerm === '') {
                        $('#search-results').empty();
                        $('#notes-container').show();
                        renderNotes();
                    }
                    else {
                        const filteredNotes = filterNotes(searchTerm);
                        renderSearchResults(filteredNotes);
                    }
                }
            }

            function scrollToNotedItem(refItemId, noteOfPart) {
                if ($(`.noted.${refItemId}`).length) {
                    if ($('.question-palette__part.-active').data('part') != noteOfPart) {
                        $('.question-palette__part[data-part="' + noteOfPart + '"]').trigger('click', false);
                        highlighterContent.on('transitionend', function () {
                            $(`.noted.${refItemId}`)[0].scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            highlighterContent.off('transitionend');
                        });
                    }
                    else {
                        $(`.noted.${refItemId}`)[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            }

            // Handle click event on "Save" button
            $('#save-note').click(function () {
                saveNote();
            });

            // Event delegation for handling delete and cancel click events
            $(document).on('click', '.notepad__item-more', function (event) {
                $('.notepad__item-more').removeClass('active');
                $(this).toggleClass('active');
            });

            $(document).on('click', '.notepad__text-confirm.-delete', function (event) {
                const noteId = $(this).closest('.notepad__item').data('noteId');
                const refNoteId = $(this).closest('.notepad__item').data('refId');
                deleteNote(noteId);
                removeNoteOnTestPanel(refNoteId)
            });

            $(document).on('click', '.notepad__delete-confirm', function (event) {
                event.stopPropagation();
            });

            $(document).on('click', '.notepad__item-title', function (event) {
                event.stopPropagation();
                var itemNote = $(this).closest('.notepad__item');
                var refItemId = itemNote.data('refId');
                var noteOfPart = itemNote.data('notePart');
                scrollToNotedItem(refItemId, noteOfPart);
            });

            $(document).on('click', '.notepad__more-item-row.-edit', function (event) {
                var editableDiv = $(this).closest('.notepad__item').find('.notepad__item-content');
                var editableDivWrap = $(this).closest('.notepad__item').find('.notepad__item-content-wrap');
                var noteId = $(this).closest('.notepad__item').data('noteId');
                var btnEditTemplate = `<div class="notepad__btns-edit">
                                    <button class="notepad__btn-cancel iot-grbt -white">Cancel</button>
                                    <button class="notepad__btn-save iot-grbt" data-note-id="${noteId}">Save</button>
                                  </div>`;

                originalNoteContent = editableDiv.text();
                $(this).closest('.notepad__item-more').removeClass('active');
                editableDiv.attr("contentEditable", "true").focus();
                editableDivWrap.append(btnEditTemplate);
                $('#notes-container').getNiceScroll().resize();
            });

            $(document).on('click', '.notepad__btn-cancel', function (event) {
                var editableDiv = $(this).closest('.notepad__item').find('.notepad__item-content');
                editableDiv.text(originalNoteContent).attr("contentEditable", "false");
                $(this).closest('.notepad__btns-edit').remove();
            });

            $(document).on('click', '.notepad__btn-save', function (event) {

                const noteId = $(this).data('note-id');
                const updatedContent = $(this).closest('.notepad__item').find('.notepad__item-content').text().trim();

                // Find the note by its unique ID.
                const noteIndex = notes.findIndex(note => note.id === noteId);

                if (noteIndex !== -1) {
                    // Update the note's content.
                    notes[noteIndex].noteText = updatedContent;

                    // Save the updated notes array to local storage.
                    // localStorage.setItem('notes_' + quizID, JSON.stringify(notes));

                    // Optionally, you can re-render the notes to display the updated
                    // content.
                    renderNotes();
                }

                $(this).closest('.notepad__item-content-wrap').find('.notepad__item-content').attr("contentEditable", "false");
                $(this).closest('.notepad__btns-edit').remove();

            });

            $(document).on('click', '.notepad__more-item-row', function (event) {
                event.stopPropagation();
                $(this).toggleClass('active');
                const confirmDeleteElm = this.querySelector('.notepad__delete-confirm');
                const confirmEditElm = this.closest(".notepad__item").querySelector('.notepad__item-content-wrap');
                document.addEventListener('click', clickDeleteItemHandler);
                document.addEventListener('click', clickEditItemHandler);

                function clickDeleteItemHandler(event) {

                    if (confirmDeleteElm) {
                        const isInsideConfirmDeleteElm = confirmDeleteElm.contains(event.target);
                        if (!isInsideConfirmDeleteElm) {
                            document.removeEventListener('click', clickDeleteItemHandler);
                            $(confirmDeleteElm).closest('.notepad__item-more').removeClass('active');
                            $(confirmDeleteElm).closest('.notepad__more-item-row').removeClass('active');
                        }
                        else {
                            if ($(event.target).is('.notepad__text-confirm')) {
                                document.removeEventListener('click', clickDeleteItemHandler);
                            }
                        }
                    }
                }

                function clickEditItemHandler(event) {

                    if (confirmEditElm) {
                        const isInsideConfirmEditElm = confirmEditElm.contains(event.target);
                        if (!isInsideConfirmEditElm) {
                            document.removeEventListener('click', clickEditItemHandler);
                            $('.notepad__btns-edit').remove();
                            $('.notepad__item-content').attr("contentEditable", "false");
                        }
                        else {
                            if ($(event.target).is('.iot-grbt')) {
                                document.removeEventListener('click', clickEditItemHandler);
                            }
                        }
                    }
                }
            });

            document.addEventListener('click', function (event) {
                const moreNote = document.querySelector('.notepad__item-more.active');
                if (moreNote) {
                    const isClickInsideMoreNote = moreNote.contains(event.target);
                    if (!isClickInsideMoreNote) {
                        $(moreNote).removeClass('active')
                    }
                }
            });

            $(document).on('click', '.notepad__text-confirm.-cancel', function (event) {
                event.stopPropagation();
                $(this).closest('.notepad__item-more').removeClass('active');
                $(this).closest('.notepad__more-item-row').removeClass('active');
            });

            function saveNote() {
                const inputTextarea = $('#user-note-input');
                const noteText = inputTextarea.val().trim();
                const selectedText = noteString;
                const noteOfPart = $('.question-palette__part.-active').data('part');
                const noteRefId = $('#js-note-content').data('id');

                if (noteText && selectedText) {
                    // Create a new note object
                    const newNote = {
                        selectedText: selectedText,
                        noteText: noteText,
                        noteOfPart: noteOfPart,
                        noteRefId: noteRefId
                    };

                    // Add the note to the notes array
                    notes.push(newNote);

                    // Save the notes array to local storage
                    // localStorage.setItem('notes_' + quizID, JSON.stringify(notes));

                    // Render the notes again
                    renderNotes();

                    // Clear the textarea content
                    inputTextarea.val('');
                    noteString = '';

                    // Hide the tooltip
                    hideHighlightTooltip();

                    //toggle new note status icon
                    newAddedNote += 1;
                    checkNewNote();
                }
            }

        }

        function checkNewNote() {
            if (newAddedNote > 0) {
                $('#js-bt-notepad').addClass('active');
            }
            else {
                $('#js-bt-notepad').removeClass('active');
            }
        }

        function removeNoteOnTestPanel(noteId) {
            // Ensure all highlights are serialised first!
            NoteIds.forEach(function (noteId) {
                noteSerialized[noteId] = userNote.serializeHighlights(noteId);
            });

            if (noteId) {
                userNote.removeHighlights(null, noteId);
                NoteIds = NoteIds.filter((id) => id !== noteId);
            }
            hideHighlightTooltip();
        }

        function renderNotes() {
            const notesContainer = $('#notes-container');
            if (notesContainer.length) {
                notesContainer.empty();

                $.each(notes, function (index, note) {
                    const noteId = uuidv4(); // Create a unique ID for each note.
                    note.id = noteId; // Save the ID into the note.
                    const noteDiv = `
                <div class="notepad__item" data-note-id="${noteId}" data-note-part="${note.noteOfPart}" data-ref-id="${note.noteRefId}">
                  <div class="notepad__item-title">${note.selectedText}</div>
                  <div class="notepad__item-content-wrap">
                    <div class="notepad__item-content">${note.noteText}</div>
                  </div>
                  <span class="notepad__item-more">
                    <span class="notepad__item-more-icon ioticon-more-vertical"></span>
                    <span class="notepad__more-card">
                      <span class="notepad__more-item-row -edit">Edit <span class="notepad__more-item-icon ioticon-edit"></span></span>
                      <span class="notepad__more-item-row">
                        Delete <span class="notepad__more-item-icon ioticon-trash-3 -delete"></span>
                        <span class="notepad__delete-confirm">Are you sure to delete this note?
                          <span class="notepad__text-confirm-wrap">
                            <span class="notepad__text-confirm -delete" data-id="${noteId}">Yes</span>
                            <span class="notepad__text-confirm -cancel">No</span>
                          </span>
                        </span>
                      </span>
                    </span>
                  </span>
                </div>
              `;

                    notesContainer.prepend(noteDiv);
                });
            }
        }

        function initNote() {
            var quizID = getQuizId();
            if (highlighterContent.length) {
                var sandbox = document.getElementById("highlighter-contents");
                window.userNote = new TextHighlighter(sandbox, {
                    version: "independencia",
                    useDefaultEvents: false,
                    color: "var(--main-color)",
                    highlightedClass: 'noted',
                    preprocessDescriptors: function (range, descriptors) {
                        var uniqueId = "hlt-" + Math.random()
                            .toString(36)
                            .substring(2, 15) +
                            Math.random()
                                .toString(36)
                                .substring(2, 15);
                        NoteIds.push(uniqueId);
                        addNoteId(uniqueId)
                        var descriptorsWithIds = descriptors.map(function (descriptor) {
                            var wrapper = descriptor[0];
                            var highlightedText = descriptor[1];
                            var offset = descriptor[2];
                            var length = descriptor[3];
                            var timestamp = $(wrapper).data('timestamp');
                            addNoteTimestamp(timestamp);
                            noteString = descriptor[1];
                            return [
                                wrapper.replace(
                                    'class="noted"',
                                    "class=\"noted " + uniqueId + "\"" + " id=\"" + uniqueId + "\""
                                ),
                                highlightedText,
                                offset,
                                length
                            ];
                        });
                        return { descriptors: descriptorsWithIds, meta: { id: uniqueId } };
                    },
                });

                $("#user-note-input").on("blur", function () {
                    if (previousRange) {
                        // Select the previous region again
                        var selection = window.getSelection();
                        selection.removeAllRanges();
                        selection.addRange(previousRange);
                    }
                });

                highlighterContent.on("mouseup", function () {
                    var selection = window.getSelection();

                    if (selection.rangeCount > 0) {
                        var range = selection.getRangeAt(0);
                        var selectedText = getSelectedText();
                        if (selectedText !== "") {
                            showHighlightControl(range);
                            // Update the previous selection with the new selection
                            previousRange = range.cloneRange();
                        }
                        else {
                            // If there is no new selection, set previousRange to null.
                            previousRange = null;
                        }
                    }
                });

                $(document).on('click', '#js-remove-note', function (event) {
                    // Ensure all highlights are serialised first!
                    NoteIds.forEach(function (noteId) {
                        noteSerialized[noteId] = userNote.serializeHighlights(noteId);
                    });

                    var noteId = $(this).data('id');
                    if (noteId) {
                        userNote.removeHighlights(null, noteId);
                        NoteIds = NoteIds.filter((id) => id !== noteId)
                        deleteNote(noteId);
                    }
                    hideHighlightTooltip();
                    newAddedNote -= 1;
                    checkNewNote();
                });

                function deleteNote(noteId) {
                    var noteRefId = $('[data-ref-id="' + noteId + '"]').data('noteId');
                    // Find a note by its unique ID.
                    const noteIndex = notes.findIndex(note => note.id === noteRefId);
                    if (noteIndex !== -1) {
                        // Remove a note from the notes array using the found index.
                        notes.splice(noteIndex, 1);

                        // Save the notes array to local storage.
                        // localStorage.setItem('notes_' + quizID, JSON.stringify(notes));

                        // Display the search results again.
                        const searchTerm = $('#note-search').val().trim().toLowerCase();
                        if (searchTerm === '') {
                            $('#search-results').empty();
                            $('#notes-container').show();
                            renderNotes();
                        }
                        else {
                            const filteredNotes = filterNotes(searchTerm);
                            renderSearchResults(filteredNotes);
                        }
                    }
                }

                function getSelectedText() {
                    if (window.getSelection) {
                        return window.getSelection().toString();
                    }
                    else if (document.selection && document.selection.type != "Control") {
                        return document.selection.createRange().text;
                    }
                    return "";
                }

                function addNoteTimestamp(timestamp) {
                    $('#js-note-content').data('timestamp', timestamp);
                    noteTimestamp = timestamp;
                }

                function addNoteId(uniqueId) {
                    $('#js-note-content').data('id', uniqueId);
                }

                clickOutNotetHandler = function clickOutNotetHandler(event) {

                    if (!highlightTooltip.is(event.target) && highlightTooltip.has(event.target).length === 0) {
                        hideHighlightTooltip();

                        cancelNote(noteTimestamp);
                    }
                }

                function addClassSavedNote(timestamp) {
                    $(`[data-timestamp="${timestamp}"]`).each(function (index, el) {
                        $(this).addClass('saved-note');
                    });
                }

                function replaceNoteToHightlight(timestamp) {
                    $(`[data-timestamp="${timestamp}"]`).each(function (index, el) {
                        $(this).removeClass('noted').addClass('highlighted');

                        const currentClass = $(this).attr('class');
                        const newClass = currentClass.replace(/note-up/g, 'hltr-');
                        $(this).attr('class', newClass);
                    });
                }

                $('#cancel-note').on('click', function (event) {
                    var timestamp = $('#js-note-content').data('timestamp');
                    cancelNote(timestamp);
                });

                $('#save-note').on('click', function (event) {
                    var timestamp = $('#js-note-content').data('timestamp');
                    addClassSavedNote(timestamp);
                });

                // const highlightHandler = hltr.highlightHandler.bind(hltr);
                const noteHandler = () => userNote.highlightHandler();

                $('#js-btn-note').on("click", function () {
                    noting = true;
                    noteHandler();
                    $(document).on('click', '#highlighter-contents', clickOutNotetHandler);
                });

                $('#js-btn-highlight').on('click', function (event) {
                    if (noting) {
                        var timestamp = $('#js-note-content').data('timestamp');
                        replaceNoteToHightlight(timestamp);
                    }
                });
            }

        }

        function initHighlighter() {
            if (highlighterContent.length) {
                var highlightIds = [];
                var sandbox = document.getElementById("highlighter-contents");
                var removeHighlightBtn = document.getElementById("js-remove-highlight");
                window.hltr = new TextHighlighter(sandbox, {
                    version: "independencia",
                    useDefaultEvents: false,
                    preprocessDescriptors: function (range, descriptors) {
                        var uniqueId = "hlt-" + Math.random()
                            .toString(36)
                            .substring(2, 15) +
                            Math.random()
                                .toString(36)
                                .substring(2, 15);
                        highlightIds.push(uniqueId);

                        var descriptorsWithIds = descriptors.map(function (descriptor) {
                            var wrapper = descriptor[0];
                            var highlightedText = descriptor[1];
                            var offset = descriptor[2];
                            var length = descriptor[3];

                            return [
                                wrapper.replace(
                                    'class="highlighted"',
                                    "class=\"highlighted " + uniqueId + "\"" + " id=\"" + uniqueId + "\""
                                ),
                                highlightedText,
                                offset,
                                length
                            ];
                        });
                        return { descriptors: descriptorsWithIds, meta: { id: uniqueId } };
                    },
                });

                var serialized = {};

                $("#user-note-input").on("blur", function () {
                    if (previousRange) {
                        // Select the previous region again
                        var selection = window.getSelection();
                        selection.removeAllRanges();
                        selection.addRange(previousRange);
                    }
                });

                highlighterContent.on("mouseup", function () {
                    var selection = window.getSelection();
                    if (selection.rangeCount > 0) {
                        var range = selection.getRangeAt(0);
                        var selectedText = getSelectedText();
                        if (selectedText !== "") {
                            showHighlightControl(range);
                            // Update the previous selection with the new selection
                            previousRange = range.cloneRange();
                        }
                        else {
                            // If there is no new selection, set previousRange to null.
                            previousRange = null;
                        }
                    }
                });

                $(document).on('click', '#js-remove-highlight', function (event) {
                    // Ensure all highlights are serialised first!
                    highlightIds.forEach(function (highlightId) {
                        serialized[highlightId] = hltr.serializeHighlights(highlightId);
                    });

                    var highlightId = $(this).data('id');
                    if (highlightId) {
                        hltr.removeHighlights(null, highlightId);
                        highlightIds = highlightIds.filter((id) => id !== highlightId)
                    }
                    hideHighlightTooltip();
                    cancelNote(noteTimestamp);
                });

                function getSelectedText() {
                    if (window.getSelection) {
                        return window.getSelection().toString();
                    }
                    else if (document.selection && document.selection.type != "Control") {
                        return document.selection.createRange().text;
                    }
                    return "";
                }

                // const highlightHandler = hltr.highlightHandler.bind(hltr);
                const highlightHandler = () => hltr.highlightHandler();

                $('#js-btn-highlight').on("click", highlightHandler);
            }

        }

        function cancelNote(timestamp) {
            $(`[data-timestamp="${timestamp}"]`).each(function (index, el) {
                const notedText = el.textContent;
                const parent = el.parentElement;
                const textNode = document.createTextNode(notedText);
                parent.replaceChild(textNode, el);
            });
            $(document).off('click', '#highlighter-contents', clickOutNotetHandler);
        }

        function showHighlightControl(range) {

            var highlightMenuWidth = highlightTooltip.outerWidth();
            var highlightMenuHalfWidth = highlightMenuWidth / 2;
            var rect = range.getBoundingClientRect();
            var noteWidth = $('#js-note-content').outerWidth();
            var viewportWidth = $(window).width();
            var highlightMenuLeft = (rect.left + rect.width / 2) - highlightMenuHalfWidth;
            var highlightMenuRight = highlightMenuLeft + highlightMenuWidth;
            var highlightNoteLeft = (rect.left + rect.width / 2) - noteWidth / 2;
            var highlightNoteRight = highlightNoteLeft + noteWidth;

            $('#js-note-content').removeAttr('style');

            // Check if the tooltip exceeds the screen size
            if (highlightMenuLeft < 0) {
                highlightMenuLeft = 0;
                highlightTooltip.removeClass("left right").addClass('left');
                $('#js-note-content').css({
                    left: highlightMenuLeft,
                    'transform': 'none'
                });
            }
            else if (highlightMenuRight > viewportWidth) {
                highlightMenuLeft = viewportWidth - highlightMenuWidth;
                highlightTooltip.removeClass("left right").addClass('right');
            }
            else {
                highlightTooltip.removeClass("left right");

                if (highlightNoteLeft < 0) {
                    $('#js-note-content').css({
                        left: (-1 * highlightMenuLeft),
                        'transform': 'none'
                    });
                }
                else if (highlightNoteRight > viewportWidth) {
                    $('#js-note-content').css({
                        left: 'initial',
                        right: (highlightMenuRight - viewportWidth),
                        'transform': 'none'
                    });
                }
            }

            highlightTooltip.css({
                top: rect.top - highlightTooltip.outerHeight(),
                left: highlightMenuLeft
            });
        }

        function showHighlightTooltip() {
            $(document).on('click', '#highlighter-contents', function (event) {
                var selection = window.getSelection(),
                    range;
                if ($(event.target).is('input, textarea')) {
                    hideHighlightTooltip(false);
                    return true;
                }
                if (selection.rangeCount > 0) {
                    range = selection.getRangeAt(0);

                    if (!range.collapsed || $(event.target).hasClass('highlighted') || $(event.target).hasClass('noted')) {
                        // Display the tooltip for the first time
                        highlightTooltip.show();
                        tooltipVisible = true;
                        highlightTooltip.removeClass('reactive-note reactive no-range');

                        if ($(event.target).hasClass('highlighted')) {
                            showHighlightControl($(event.target)[0]);
                            var highlightId = $(event.target).attr('id');
                            $('#js-remove-highlight').data('id', highlightId);
                            highlightTooltip.addClass('reactive');
                        }
                        if ($(event.target).hasClass('noted')) {
                            showHighlightControl($(event.target)[0]);
                            var noteId = $(event.target).attr('id');
                            $('#js-remove-note').data('id', noteId);
                            highlightTooltip.addClass('reactive-note');
                        }
                        if (range.collapsed) {
                            highlightTooltip.addClass('no-range');
                            noteTimestamp = '';
                            $('#js-note-content').data('timestamp', '');
                        }
                    }
                    else if (!highlightTooltip.is(event.target) && highlightTooltip.has(event.target).length === 0) {
                        hideHighlightTooltip();
                    }
                }

            });

            $('#js-btn-highlight').on('click', function (event) {
                event.preventDefault();
                hideHighlightTooltip();
                clearUserSelection();
            });

            var divParent = $('.test-contents, .test-panel');
            divParent.on('scroll', function () {
                if (tooltipVisible) {
                    hideHighlightTooltip();
                }
            });
        }

        function hideHighlightTooltip(clearSelection = true) {
            $('#user-note-input').val('');
            highlightTooltip.hide();
            tooltipVisible = false;
            noting = false;
            highlightTooltip.removeClass('reactive reactive-note no-range');
            noteInput.hide();
            userSelectedRange = '';
            if (clearSelection) {
                clearUserSelection();
            }
            $(document).off('click', '#highlighter-contents', clickOutNotetHandler);
        }

        // Get user selected text.
        function getUserSelection() {
            let selectedText = '';
            if (typeof window.getSelection !== 'undefined') {
                selectedText = window.getSelection().toString();
            }
            else if (typeof document.selection !== 'undefined' && document.selection.type === 'Text') {
                selectedText = document.selection.createRange().text;
            }
            return selectedText;
        }

        function clearUserSelection() {
            if (window.getSelection) {
                if (window.getSelection().empty) { // Chrome, Firefox, Opera, Safari
                    window.getSelection().empty();
                }
                else if (window.getSelection().removeAllRanges) { // IE
                    window.getSelection().removeAllRanges();
                }
            }
            else if (document.selection) {  // IE 8 and below
                document.selection.empty();
            }
        }

        function showNotePad() {

            $('#js-bt-notepad, .notepad__close-icon').click(function (event) {
                $('body').toggleClass('notepad-open');
                if ($('body').hasClass('notepad-open')) {
                    var notePadWidth = $('#notepad').outerWidth();
                    highlighterContent.css({
                        'width': `calc(100% - ${notePadWidth}px)`,
                        'margin-left': 0
                    });
                }
                else {
                    highlighterContent.removeAttr('style');
                }

                $('.test-panel, .test-contents').getNiceScroll().hide();
                highlighterContent.on('transitionstart', function () {
                    $('body').addClass('transitioning');
                    $('.test-panel, .test-contents').getNiceScroll().hide();
                    highlighterContent.off('transitionstart');
                });

                highlighterContent.on('transitionend', function () {
                    $('body').removeClass('transitioning');
                    $('.test-panel, .test-contents').getNiceScroll().show().resize();
                    highlighterContent.off('transitionend');
                });
            });

            $('#js-bt-notepad, #notepad').click(function (event) {
                newAddedNote = 0;
                $('#js-bt-notepad').removeClass('active');
            });

            $('#js-btn-note').click(function (event) {
                $(".highlight-box__note-content").show();
            });

            $('#cancel-note').click(function (event) {
                $(highlightTooltip, noteInput).hide();
                userSelectedRange = '';
                $('#user-note-input').val('');
                clearUserSelection();
            });
        }

        function setNotepadHeight() {
            var headerHeight = $('.realtest-header').outerHeight();
            var questionPaletteHeight = $('.question-palette').outerHeight();
            var windowHeight = $(window).height();
            var notepadHeight = windowHeight - (headerHeight + questionPaletteHeight);
            $('#notepad').css({
                height: notepadHeight,
            });
        }

        // Run nice scroll bar for notes.
        function runNotesNiceScroll() {
            var noteContainer = $('#notes-container');
            noteContainer.niceScroll({
                autohidemode: true,
                cursorborderradius: 6,
                cursorwidth: "2px",
                cursorcolor: "#dfdfdf",
            });
        }

        // Clear data note.
        function clearDataNote() {
            var quizID = getQuizId();
            localStorage.removeItem('notes_' + quizID);
        }

        // End NOTE/HIGHLIGHT functions
        //-----------------------------------

        window.initNote = initNote;
        window.showNotePad = showNotePad;
        window.initHighlighter = initHighlighter;
        window.setNotepadHeight = setNotepadHeight;

        window.loadNotepad = () => {
            showNotePad();

            //Handle notepad.
            setupNoteApp();

            setNotepadHeight();
            $(window).resize(function () {
                setNotepadHeight();
            });

            // Run nice scroll bar for notes.


            // Show button note and highlight when black out the text.
            initHighlighter();
            initNote();
            // Show tooltip.
            showHighlightTooltip();
            if ($(`.question-palette__item`).length > 0) {
                $(`.question-palette__item`)[0].click();
            }
        };

        window.loadNotepad();

        if(document.querySelectorAll('.selectpicker').length > 0){
            $('.selectpicker').selectpicker();
        }
    });


};




let loader = setInterval(() => {
    window.preload();
    if ($(`.question-palette__item`).length > 0) {
        if(document.body.classList.contains('-start')){
            
            if(jQuery('#take-test__player').length > 0){
                
            } else {
                window.runTimeClock(window.timeEndReading);
                window.runTestPanelNiceScroll();
                window.runAutoSave();
            }
            clearInterval(loader);
        } else if(document.body.classList.contains('-resume')){
            
            // window.runTimeClock(window.timeEndReading);
            window.runTestPanelNiceScroll();
            clearInterval(loader);
            // window.runAutoSave();
            jQuery('#modal-resume').modal('show');
        } else if(document.body.classList.contains('-result')){
            clearInterval(loader);
            var windowWidth = $(window).width();
            var elements = $('.test-panel, .test-contents');
            if (windowWidth > 1024) {
                elements.niceScroll({
                    autohidemode: false,
                    cursorborderradius: 6,
                    cursorwidth: "8px",
                    cursorcolor: "#dfdfdf",
                    horizrailenabled: false,
                });
                initNiceScroll = true;
            } else {
                elements.each(function (index, el) {
                    $(el).getNiceScroll().remove();
                    initNiceScroll = false;
                });
            }
            
            window.updateAnsweredQuestions();
        } else {
            console.log('Unauthorized');
        }
    }

}, 500);


