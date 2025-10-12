<?php 
/**
 * Class Writing
 */
class Writing {
    public $post_id                     = null;
    public $quiz                        = [];
    public $course                      = [];
    public $category                    = '';
    public $_id                         = '';
    public $result_id                   = '';
    public $sections                    = [];
    public $seconds                     = 60;
    public $default                     = 3600;
    public $resume                      = false;
    public $result                      = false;
    public $review                      = false;
    public $solution                    = false;
    public $session                     = [];
    public $user_data                   = null;
    public $_result                     = null;
    public $_q                          = [];
    public static $sections_html        = '';
    public static $sections_content_html = '';
    public static $current_p_index      = 0;
    public static $p_counter            = 0;
    public static $c_counter            = 0;
    public static $pallete              = '';
    public $balance                     = 0;
    public $evaluation_reason           = '';

    public $enrolled_for                = [];
    public $dist_url                    = '/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist';

    public $HAS_TASKS_1                 = false;
    public $HAS_TASKS_2                 = false;

    public $IS_ALL_TASKS_REVIEWED       = true;
    public $IS_ALL_TASKS_PAID           = true;
    public $TASK1_SELECTOR_LAYOUT       = [];
    public $TASK2_SELECTOR_LAYOUT       = [];

    public $IS_NORMAL_EVALUATION_ENABLE = false;
    public $IS_AI_EVALUATION_ENABLE     = false;

    public $HAS_TASK1_PLAN              = false;
    public $TASK1_PLAN                  = [];
    public $HAS_TASK2_PLAN              = false;
    public $TASK2_PLAN                  = [];
    public $HAS_AI_TASK1_PLAN           = false;
    public $AI_TASK1_PLAN               = [];
    public $HAS_AI_TASK2_PLAN           = false;
    public $AI_TASK2_PLAN               = [];
    public $PROQYZ_EVALUATION           = false;
    public $is_fullmock_test            = false;
    
    public $Task_2_response = array(
        1 => "Answer is completely unrelated to the task",
        2 => "Barely responds to the task",
        3 => "Does not adequately address any part of the task",
        4 => "Responds to the task in a minimal way; not well supported",
        5 => "Addresses the task only partially; may be inappropriate in places",
        6 => "Addresses all parts of the task; may become unclear or repetitive",
        7 => "Addresses all parts of the task but some ideas may lack focus",
        8 => "Sufficiently addresses all parts of the task with supported ideas",
        9 => "Fully addresses all parts of the task; well supported ideas"
    );
    
    public $Task_1_response = array(
        1 => "Answer is completely unrelated to the task", // 9.0
        2 => "Answer is barely related to the task",       // 8.5
        3 => "Fails to address the task",                   // 8.0
        4 => "Attempts to address the task but does not cover all key features/bullet points", // 7.5
        5 => "Generally addresses the task; no clear overview", // 7.0
        6 => "Addresses the requirements of the task",          // 6.5
        7 => "Covers most of the requirements of the task",     // 6.0
        8 => "Covers all requirements of the task sufficiently", // 5.5
        9 => "Fully satisfies all the requirements of the task" // 5.0
    );
    
    public $coherence_lines = array(
        1 => "Fails to communicate any message",
        2 => "Has very little control of organisational features",
        3 => "Does not organise ideas logically; may not indicate a logical relationship between ideas",
        4 => "Presents information and ideas but not coherent; no clear progression in the response",
        5 => "Presents information with some organisation; lack of overall progression",
        6 => "Logically organises information and ideas; cohesive devices may be under-/over-used",
        7 => "logically organised throughout; cohesive devices used flexibly; a few lapses",
        8 => "Sequences information logically; uses paragraphing appropriately",
        9 => "Uses cohesion that attracts no attention; skilfully manages paragraphing"
    );
    
    public $Lexical_lines = array(
        1 => "Can only use a few isolated words",
        2 => "Extremely limited range of vocabulary",
        3 => "Errors may severely distort the message",
        4 => "Uses only basic vocabulary; has limited control",
        5 => "Uses a limited range of vocabulary; makes noticeable errors",
        6 => "Uses an adequate range of vocabulary; makes some errors",
        7 => "Uses a sufficient range of vocabulary; occasional errors",
        8 => "Uses a wide range of vocabulary fluently; rare errors",
        9 => "Uses a wide range of vocabulary with sophisticated control"
    );
    
    public $grammatical_lines = array(
        1 => "Cannot use sentence forms at all",
        2 => "Cannot use sentence forms except in memorised phrases",
        3 => "Errors in grammar and punctuation predominate and distort the meaning",
        4 => "Uses only a very limited range of structures; errors predominate",
        5 => "Uses limited range of structures; frequent grammatical errors and punctuation",
        6 => "Makes some errors in grammar and punctuation; rarely reduce communication",
        7 => "Produces frequent error-free sentences; may make a few errors",
        8 => "Uses a wide range of structures; the majority are error-free",
        9 => "Uses a wide range of structures with full flexibility and accuracy"
    );

    public function __construct($params = []) {
        global $wpdb, 
        $table_proqyz_quiz_progress, 
        $table_proqyz_quiz_progress_meta, 
        $table_proqyz_groups, 
        $table_proqyz_quiz_review, 
        $table_proqyz_quiz_review_meta;

        $params = (object) $params;
        $this->category     = isset($params->category) ? (string) $params->category : '';
        $this->_id          = isset($params->_id) ? (string) $params->_id : '';
        $this->sections     = isset($params->sections) ? (array) $params->sections : [];
        $this->quiz         = isset($params->quiz) ? (object) $params->quiz : (object) [];
        $this->course       = isset($params->course) ? (object) $params->course : null;
        $time               = isset($this->quiz->time)? (object) $this->quiz->time : null;
        $this->resume       = isset($params->resume) ? (bool) $params->resume : false;
        $this->result       = isset($params->result) ? (bool) $params->result : false;
        $this->review       = isset($params->review) ? (bool) $params->review : false;
        $this->solution     = isset($params->solution) ? (bool) $params->solution : false;
        $this->post_id      = isset($params->post_id) ? $params->post_id : null;
        $this->session      = isset($params->session)? (object) $params->session : [];
        $this->user_data    = isset($params->user_data)? (object) $params->user_data : null;
        $this->result_id    = isset($params->result_id)? (int) $params->result_id : null; 
        $this->_q           = isset($params->_q)? (object) $params->_q : (object) []; 
        $this->_result      = isset($params->_result)? (object) $params->_result : null; 
        $minuts = 0;


        if( $this->result || $this->review ) {
            # check if student paid or enrolled for tasks
            $db_student_id      = $this->_result->user_id;
            $db_result_id       = $this->result_id;
            $check_payment_for_tasks = get_user_meta($db_student_id,"st_proqyz__evaluation-".($db_result_id), true);
            
            if( $check_payment_for_tasks ) {
                $this->enrolled_for = json_decode( $check_payment_for_tasks, true ); 
            }
        }
        
        if( count( $this->sections ) > 0 ) {
            foreach( $this->sections as $skey => $section ) {
                $section        = (object) $section;
                $section_id     = $section->_id;
                $order          = (int) $section->order;
                $time           = isset($section->time)? (object) $section->time : null;
                $sectionTime    = isset($time->sectionTime)? (object) $time->sectionTime : null;

                if($sectionTime !== null){
                    $minn = isset($sectionTime->mm)? (int) $sectionTime->mm : 0;

                    if($minn <= 0){
                        if($order == 1){
                            $minuts += 20;  // task 1 - 20 min
                        } else if($order == 2){
                            $minuts += 40; // task 2 - 40 min
                        }
                        
                    } else {
                        $minuts += $minn;
                    }
                    
                }

                
                if( $order == 1 ) { 
                    $this->HAS_TASKS_1 = true; 
                } else if( $order == 2 ) {
                    $this->HAS_TASKS_2 = true;
                }
                

                # only works for result or review context
                # check if tasks reviewed or not
                if( $this->result || $this->review ) {
                    $db_result_id       = $this->result_id;
                    $db_student_id      = $this->_result->user_id;

                    $get_review  = $wpdb->get_row("SELECT `ID` FROM {$table_proqyz_quiz_review} WHERE `result_id` = '$db_result_id' AND `user_id` = '$db_student_id' AND `status` = 1 AND `section_id` = '$section_id' LIMIT 1");
                    if( $get_review ) {

                    } else {
                        # if non of the task review then mark all tasks reviewded to false
                        $this->IS_ALL_TASKS_REVIEWED = false;

                        # also if non of the task si not review so, its also possible these tasks are also not enrolled
                        if( !array_key_exists($section_id, (array) $this->enrolled_for) ) {
                            if( $order == 1 ) {
                                $this->TASK1_SELECTOR_LAYOUT[] = (object) [
                                    "label" => "Task ".($skey+1),
                                    "value" => $section_id
                                ];
                            } else if( $order == 2 ) {
                                $this->TASK2_SELECTOR_LAYOUT[] = (object) [
                                    "label" => "Task ".($skey+1),
                                    "value" => $section_id
                                ];
                            } 
                            $this->IS_ALL_TASKS_PAID = false;
                        }
                    }

                }

            }
        }


        $this->default = $minuts * 60;
        if( $this->resume ) {
            $this->seconds      = isset($params->seconds)? (int) $params->seconds : $this->default;
            
        } else if( $this->result ) {
            $this->seconds      = isset($params->seconds)? (int) $params->seconds : $this->default;
            
        } else {
            $this->seconds      = $this->default;
        }

        
        if(isset($this->course)){
            $course_q = (object) $this->course;
            $main_course = isset($course_q->course) ? (object) $course_q->course : null;
            $evaluation = isset($main_course->_evaluation)? (object) $main_course->_evaluation : null;
            if($evaluation !== null){
                if( isset($evaluation->status) && $evaluation->status == 1) {
                    if(isset($evaluation->writing) && $evaluation->writing == 1){
                        $this->PROQYZ_EVALUATION = true;
                    }
                }
            }
        }

        

        if( !$this->IS_ALL_TASKS_PAID && !$this->IS_ALL_TASKS_REVIEWED) {
            $this->check_evaluation_plans();
        }

        if( $this->session && isset($this->session->session_category) && $this->session->session_category === "fullmock-test" ) {
            $this->is_fullmock_test  = true;
        }
        
    }  

    public function get_sections_html() {
        if (count($this->sections) > 0) {
            foreach ($this->sections as $skey => $section) {
                # main section object
                $section = (object) $section;
                $title   = (string) $section->title;
                $content = (string) $section->content;

                # section html buffer module
                ob_start(); ?>
                <section 
                    id="part-<?php echo $skey+1; ?>" 
                    class='<?php echo $skey === 0 ? "test-contents ckeditor-wrapper -show" : "test-contents ckeditor-wrapper"; ?>' 
                    style="overflow-y:scroll;outline:none;display:none"
                >
                    <div class="test-contents__paragragh">
                        <?php echo $content; ?>
                    </div>
                </section>
                <?php 
                self::$sections_html .= ob_get_clean();
                # clean and store the buffer and increment it
            }

            return self::$sections_html;
        }
    }

    public function get_sections_user_content(){
        if (count($this->sections) > 0) {
            foreach ($this->sections as $skey => $section) {
                # main section object
                $section = (object) $section;
                $title   = (string) $section->title;
                $content = (string) $section->content;
                $section_id = $section->_id;
                $section_order = $section->order;

                /**
                 * Resume logic
                 */
                $user_answer = '';
                if( $this->resume ) {
                    $response           = (object) $this->user_data;
                    $quiz_details       = isset($response->quiz)? (object) $response->quiz : null;
                    $user_answers       = isset($response->answers)? (array) $response->answers : [];
                    $current_question   = $this->findObjectByKeyValue($user_answers, 'id', $section_id);

                    if( $current_question ) {
                        $input_type     = isset($current_question->input_type)? $current_question->input_type : '';
                        $q_type         = isset($current_question->q_type)? $current_question->q_type : ''; 
                        $input_class    = isset($current_question->class)? $current_question->class : ''; 
                        $q_total        = isset($current_question->total)? (int) $current_question->total : 0;
                        if( $input_type == "textarea" && $q_type == 'writing-essay' && $input_class == "-checked" ) {
                            $fill_answer = true;
                            $user_answer = isset($current_question->answer)? $current_question->answer : '';
                            
                        }  
                    }
                }

                # section html buffer module
                ob_start(); ?>
                <section 
                    id="part-questions-<?php echo $skey+1; ?>" 
                    class='<?php echo $skey === 0 ? "test-panel -show" : "test-panel"; ?>' 
                    style="overflow-y:scroll;outline:none;display:none"
                >
                    <div class="writing-box__answer-wrapper">
                        <div class="form-item js-form-item form-type-textarea js-form-type-textarea form-item-field-answer-task-1 js-form-item-field-answer-task-1 form-no-label form-group">
                            <div class="form-textarea-wrapper">
                                <textarea 
                                    data-section-id="<?php echo $section_id; ?>"
                                    data-section-order="<?php echo $section_order; ?>"
                                    data-q_type="writing-essay" 
                                    data-input_type="textarea" 
                                    data-num="<?php echo $skey+1; ?>" 
                                    data-id="q-<?php echo $skey+1; ?>" 
                                    data-part="<?php echo $skey+1; ?>" 
                                    id="q-<?php echo $skey+1; ?>" 
                                    class="question__input task-item__answer writing-box__answer form-control form-textarea resize-vertical" 
                                    spellcheck="false" 
                                    placeholder="Type here.." 
                                    data-question-item="<?php echo $skey+1; ?>" 
                                    name="field_answer_task_<?php echo $skey+1; ?>" 
                                    rows="5" 
                                    cols="60"
                                ><?php echo $user_answer; ?></textarea>
                            </div>
                        </div>
                        <div class="writing-box__footer">
                            <div class="writing-box__words-count">
                                Words Count: <span class="writing-box__words-num"><?php echo $this->countWords($user_answer); ?></span>
                            </div>
                        </div>
                    </div>

                </section>
                <?php 
                self::$sections_content_html .= ob_get_clean();
                # clean and store the buffer and increment it
            }

            return self::$sections_content_html;
        }
    }

    /**
     * pallete
     */
    public function get_sections_pallete(){
        $total_q_counter    = 0;
        $report             = null;

        if( $this->result ) { $report = (object) $this->get_report(); }

        if (count($this->sections) > 0) {
            foreach ($this->sections as $skey => $section) {
                # main section object
                $section            = (object) $section;
                $title              = (string) $section->title;
                $_id                = (string) $section->_id;
                $section_id         = $section->_id;
                $section_order      = $section->order;


                ob_start(); ?>
                <div id="navigation-bar-<?php echo $skey+1; ?>" class="question-palette__part <?php echo ($skey == 0)? '-active' : ''; ?>" data-part="<?php echo $skey+1; ?>" data-questions="1">
                    <div class="question-palette__part-title">
                        Part <?php echo $skey+1; ?> <span>:</span>
                    </div>
                    <div class="question-palette__items-group" style="display:none;">
                        <span 
                            class="question-palette__item" 
                            data-p="<?php echo $skey+1; ?>" 
                            data-num="<?php echo $skey+1; ?>"
                        >
                            <?php echo $skey+1; ?>
                        </span>
                    </div>
                </div>
                <?php self::$pallete .= ob_get_clean();


            }
        }
        return self::$pallete;
    }


    /**
     * changes @1.2.1.2
     */
    public function get_writing_styles(){
        ob_start(); ?>
        <style>
            .w-4px{
                width: 4px !important;
                height: initial !important;
            }

            .h-4px{
                height: 4px !important;
                width: initial !important;
            }

            .-show {
                display: block !important;
            }

            .d-none {
                display: none !important;
            }

            .h-50-2 {
                width: 100% !important;
                height: calc(50% - 2px);
            }

            .w-50-2 {
                height: 100% !important;
                width: calc(50% - 2px);
            }

            .modal-open .modal-backdrop.show {
                opacity: 0.5;
            }

            @media (max-width: 767px){
                .realtest-header__bt-review {
                    display: flex;
                }
            }

            @media (min-width: 768px) and (max-width: 1024px){
                .realtest-header__bt-review {
                    display: flex;
                }
            }

            .page.writing-essay-page {
                margin: 60px auto;
            }

            .d-flex {
                display: flex !important;
            }

            .flex-row {
                flex-direction: row !important;
            }

            .align-items-center {
                align-items: center !important;
            }

            .justify-content-between {
                justify-content: space-between;
            }

            .sp_col-2 {
                display: none;
            }

            .aside-enable .sp_col-auto.sp_col-1 {
                width: calc(100% - 390px);
            }

            .col-xs-12.sp_col-auto.sp_col-1 {}

            .aside-enable .sp_col-2 {
                display: block;
                width: 390px;
            }

            .aside-enable .container {
                
            }

            .review__container {
                height: calc(100vh - 62px);
            }

            li.nav-item.active button {color: white;background: #eea155;}

            .show-notepad span.hide-notepad-text {
                display: inline-flex;
            }
            .show-notepad span.hide-notepad-text {
                display: none;
            }

            .note-fontname {
                display: none;
            }

            .note-btn-group.note-insert button:nth-child(2) {
                display: none;
            }

            .note-btn-group.note-view button:nth-child(3) {
                display: none;
            }

            a.answer__title[aria-expanded="true"] .review_btn {
                background: orange;
                color: white;
            }


            .accordion-parent .accordion-card__header {
                background: #284664;
                color: #fff;
                padding: 10px 10px 15px;
                border-radius: 5px;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            

            .accordion-card__header {
                
            }

            .accordion-card__header h5{
                color: #fff;
                font-size: 12px;
                font-weight: 400;
                padding-left: 10px;
                padding-right: 10px;
                height: 40px;
                margin: 0;
            }


            label.checkbox {
                font-weight: 500;
                font-size: 15px;
                margin: 2px 0 3px;
            }

            .checkbox-inline {
                width: 100%;
            }

            .checkbox-inline {
                padding: 0;
            }


            .btn-primary {
                color: #fff;
                background-color: #284664;
                border-color: #284664;
            }

            span.show-notepad-text {
                display: none;
            }

            .show-notepad span.show-notepad-text {
                display: inline-block !important;
            }


            button:disabled {
                opacity: 0.6;
                cursor: no-drop;
            }

        </style>
        <style>


            .test-submit-page .card-successful__col {
                width: 100%;
                flex: 1;
                padding: 0;
            }


            .test-submit-page .card-successful__box {
                width: 100%;
                height: 100%;
                max-width: 510px;
                padding: 25px 5px;
                min-height: 222px;
                margin: 0 auto;
                border-radius: 4px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .test-submit-page .service-price-box__contents {
                background-color: #fff;
                flex: 1;
                display: flex;
                flex-direction: column;
                padding: 25px 30px;
                border-radius: 4px;
                width: 100%;
            }
            .test-submit-page .service-price-box__contents {
                padding: 0 !important;
            }

            
            .test-submit-page .service-price-box__price {
                display: flex;
                border: 1px solid #C76378;
                position: relative;
                align-items: center;
                padding: 20px;
                justify-content: space-between;
                margin-bottom: 10px;
            }

            .test-submit-page.-writing .service-price-box__price {
                border: 1px solid #F9A95A;
            }


            .test-submit-page .service-price-box__col-name {
                padding-right: 10px;
                text-align: left;
            }

            .test-submit-page .service-price-box__service-name {
                font-weight: 800;
                font-size: 14px;
                color: #282828;
            }

            .test-submit-page .service-price-box__service-task {
                font-size: 14px;
                margin: 0;
            }

            .test-submit-page .service-price-box__col-price {
                text-align: right;
            }

            .test-submit-page .service-price-box__item-price {
                font-size: 24px;
                font-weight: 700;
                color: #284664;
            }

            .test-submit-page .service-price-box__item-price.-en {
                font-size: 16px;
                color: #8b8b8b;
                font-weight: 600;
            }

            .test-submit-page .service-price-box__btn-change {
                font-size: 14px;
                text-decoration: underline;
                color: #32b4c8;
                cursor: pointer;
                -moz-transition: all ease 0.2s;
                -o-transition: all ease 0.2s;
                -webkit-transition: all ease 0.2s;
                transition: all ease 0.2s;
                align-self: flex-start;
                margin-bottom: 20px;
            }

            .test-submit-page .service-price-box__btn-wrap {
                margin-top: auto;
            }

            .test-submit-page.-writing .service-price-box__btn {
                background-color: #F9A95A;
                color: #fff;
            }
            .test-submit-page .service-price-box__btn {
                width: 100%;
                max-width: 236px;
                margin: 0 auto;
                background-color: #C76378;
                color: #fff;
            }
            .test-submit-page .iot-bt {
                width: 100%;
                max-width: 236px;
                margin: 0 auto;
            }
            button.iot-bt {
                line-height: normal;
            }

            button.ielts-lms-result-show-button {
                margin: 20px auto;
                width: 100%;
                text-align: center;
                min-height: 55px;
                border: 1px solid #284664;
                font-size: 17px !important;
                color: #284664;
                font-weight: bold;
            }
            
        </style>
        <?php echo ob_get_clean();
    }

    /**
     * changes @1.2.1.2
     */
    public function get_layout(){
        if( 1 == 1 ) {
            
            $class_body = '';
            if(is_user_logged_in()){
                $class_body = $this->resume? '-resume' : '-start';
                $class_body .= ' user-logged-in ';
            } else {
                $class_body .= ' anonymous-user';
            }

            $time_limit     = 1;
            # if its course
            $course = isset($this->session->course)? (object) $this->session->course : null;
            $practice_mode = isset($course->_practice)? (object) $course->_practice : null;
            $practice_enable = isset($practice_mode->status)? $practice_mode->status : false;
            $practice_mode_audio_controls = isset($practice_mode->audioControls)? $practice_mode->audioControls : false;
            $practice_mode_time_limit_disabled = isset($practice_mode->quizTimer)? $practice_mode->quizTimer : 0;

            if($practice_enable) {
                $audio_controls = $practice_mode_audio_controls;
                # true means disabled time limit
                if($practice_mode_time_limit_disabled) {
                    $time_limit = 0;
                }
                
            }

            ob_start(); ?>
            <!DOCTYPE html>
            <html <?php language_attributes(); ?>>
            <?php echo $this->header(); ?>
            <body class="writing-test show-palette take-test-page -practice-mode user-logged-in has-glyphicons <?php echo $class_body; ?>">
                <div class="dialog-off-canvas-main-canvas js-attempt-only-writing">

                    <header class="realtest-header ">
                        <span class="realtest-header__logo practice-item__icon -writing d-none-sm-550px"></span>
                        <?php if(isset($this->quiz->title)) { ?>
                        <div class="d-none-sm-550px"><?php echo $this->quiz->title; ?></div>
                        <?php } ?>
                    
                        <div class="realtest-header__time <?php echo ($time_limit == 1)? '' : 'd-none'; ?>">
                            <span class="realtest-header__time-clock" data-timer="<?php echo $time_limit; ?>" data-current-time="" data-time="<?php echo $this->seconds; ?>" data-duration-default="<?php echo $this->default; ?>" id="time-clock">
                                <span class="realtest-header__time-val">-:-</span>
                                <span class="realtest-header__time-text">minutes remaining</span>
                                
                            </span>
                        </div>

                        <div class="realtest-header__btn-group">
                            <div class="realtest-header__btn-save save_hidden">
                                Saved<span class="ioticon-check-v2"></span>
                            </div>
                            <div class="realtest-header__icon -note" id="js-bt-notepad"></div>
                            <div class="realtest-header__icon -full-screen" id="js-full-screen" data-original-title="Full Screen Mode" data-placement="bottom" data-trigger="hover"></div>
                            <?php if( !$this->is_fullmock_test ){ ?>
                                <button class="realtest-header__bt-review" data-target="#modal-review-test">
                                    <span class="ioticon-review"></span>Review
                                </button>
                                <button class="realtest-header__bt-submit">
                                    Submit
                                </button>
                            <?php } ?>
                            
                            <?php if( $this->is_fullmock_test ) { ?>
                                <button class="realtest-header__bt-fullmock-test-submit">
                                    Submit
                                </button>
                            <?php } ?>    
                        </div>

                    </header>

                    <div class="page take-test">

                        <div class="highlight-box" id="highlight-box" style="display:none;">
                            <button class="highlight-box__btn -note" id="js-btn-note">Note</button>
                            <button class="highlight-box__btn -remove-note" id="js-remove-note">Remove note</button>
                            <button class="highlight-box__btn -highlight" id="js-btn-highlight">Highlight</button>
                            <button class="highlight-box__btn -remove" id="js-remove-highlight">Remove highlight</button>
                            <div class="highlight-box__note-content" id="js-note-content" style="display:none;">
                                <textarea name="" id="user-note-input" class="highlight-box__textarea form-control" rows="3" placeholder="Please enter your notes"></textarea>
                                <div class="highlight-box__note-buttons">
                                    <button class="highlight-box__note-btn btn -save" id="save-note">Save</button>
                                    <button class="highlight-box__note-btn btn -cancel" id="cancel-note">Cancel</button>
                                </div>
                            </div>
                        </div>

                        <div class="notepad" id="notepad" style="height:820px;">
                            <span class="notepad__close-icon ioticon-x"></span>
                            <h5 class="notepad__title">Notepad</h5>
                            <form class="notepad__search-form" action="" method="POST" role="form">
                                <div class="notepad__search">
                                    <input type="text" name="noteSearch" class="form-control notepad__input" id="note-search" placeholder="Search for your note..." />
                                    <button type="button" class="notepad__search-icon ioticon-search"></button>
                                </div>
                                <div class="notepad__search-results" id="search-results"></div>
                            </form>
                            <div class="notepad__item-wrap" id="notes-container" style="overflow:hidden;outline:none;" tab-index="1"></div>
                        </div>

                        <div class="take-test__body">

                            <div class="region region-content">
                                <article role="article">
                                    <div class="take-test__board highlighter-context" id="highlighter-contents">
                                        <div id="split-one" class="take-test__split-item">
                                            <?php echo $this->get_sections_html(); ?>
                                        </div>
                                        <div id="split-two" class="take-test__split-item">
                                            <?php echo $this->get_sections_user_content(); ?>
                                            <div class="test-panel__nav">
                                                <div class="test-panel__nav-buttons" id="js-btn-wrap" data-part-show="0">
                                                    <button class="test-panel__nav-btn -prev -disabled" id="js-btn-previous">
                                                        <span class="ioticon-prev-icon"></span>
                                                    </button>
                                                    <button class="test-panel__nav-btn -next" id="js-btn-next">
                                                        <span class="ioticon-next-icon"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php echo $this->login_notice(); ?>
                                </article>
                            </div>
                            <div class="take-test__bottom-palette">
                                <div class="question-palette">
                                    <div class="question-palette__list-item" id="question-palette-table">
                                        <?php echo $this->get_sections_pallete(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--begin::MODALS-->
                        <?php echo $this->modals(); ?>
                        <!--end::MODALS-->
                    </div>

                </div>
                <?php echo $this->footer(); ?>
            </body>
            </html>
            <?php return ob_get_clean();
        }
    }

    /**
     * changes @1.2.1.2
     */
    public function get_result_layout(){
        $class_body         = '-result';
        
        // $sections_html      = $this->get_sections_result_html();
        // $sidebar            = $this->get_sidebar();
        $current_url        = home_url($_SERVER['REQUEST_URI']);
        $review_url         = str_replace('result','review', $current_url);
        $result_url         = str_replace('review','result', $current_url);
        $student_id         = $this->_result->user_id;


        # current user condition & role check
        $c_user_id          = get_current_user_id();
        $im_role            = "student";
        $c_user             = get_userdata($c_user_id);
        if (!$c_user) { return null; }
        $user_roles = $c_user->roles;
        if (in_array("administrator", $user_roles)) {
            $im_role = "admin";                    
        } elseif (in_array("teacher", $user_roles)) {
            $im_role = "teacher";                    
        } else {
            $im_role = "student";
        }
        
        ob_start(); ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <?php echo $this->header(); ?>
        <body class="writing-test writing-test-result <?php echo $class_body; ?>">

            <!--begin::center content-->
            <div class="dialog-off-canvas-main-canvas js-attempt-only-writing">

                <header class="realtest-header ">
                    <span class="realtest-header__logo practice-item__icon -writing "></span>
                    <?php if(isset($this->quiz->title)) { ?>
                        <div class=""><?php echo $this->quiz->title; ?></div>
                    <?php } ?>

                    <div class="realtest-header__time d-none" style="display:none;">
                        <span class="realtest-header__time-clock" data-current-time="" data-time="<?php echo $this->seconds; ?>" data-duration-default="<?php echo $this->default; ?>" id="time-clock">
                            <span class="realtest-header__time-val">-:-</span>
                            <span class="realtest-header__time-text">minutes remaining</span>
                        </span>
                    </div>
                    <div class="realtest-header__btn-group">
                        <?php if($this->review){ ?>
                        <a class="realtest-header__bt-submit" href="<?php echo $result_url; ?>">
                            View as Student
                        </a>
                        <?php } ?>
                        <?php if(!$this->review && $this->result){ 
                            if($im_role == "admin" || $im_role == "teacher"){ ?>
                                <a class="realtest-header__bt-submit" href="<?php echo $review_url; ?>">
                                    View as Instructor
                                </a>
                            <?php } else { ?>
                                <button class="realtest-header__bt-submit -writing--btn" onClick="window.close()">
                                    Close
                                </button>
                            <?php }    
                        } ?>

                    </div>
                </header>

                <div class="page writing-essay-page">
                    <div class="take-test__body" style="justify-content: center;display:block;">
                        <div>

                            <div class="container">
                                <div class="region region-breadcrumb">
                                    <div class="sitemap"> 
                                        <a href="/">Home</a> 
                                        <span>/</span> 
                                        <a><?php echo $this->quiz->title; ?></a>
                                    </div>
                                </div>
                            </div>

                            <div class="container">
                                <h1>[user ] <?php echo $this->quiz->title; ?></h1>
                            </div>

                            <div class="container">
                                <div class="region region-content">
                                    <div class="row">
                                        <?php echo $this->result_user_content_area(); ?>
                                        <?php echo $this->result_sidebar_area(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="container">
                                <?php if(isset($this->session->session_category)){
                                    if($this->session->session_category == "course"){
                                        $course_id = $this->session->course_id;
                                        $course_url = st_proqyz_get_course_url( $course_id );
                                        ?>
                                        <button class="ielts-lms-result-show-button" onclick="window.location.href = '<?php echo $course_url; ?>' " type="button" role="button">Back to the course</button>
                                        <?php 
                                    }
                                } ?>
                            </div>

                            <?php if(!$this->review) { ?>
                                <div class="row">
                                    <div class="col-12">
                                        <?php echo do_shortcode("[proqyz-result-page result_id='$this->result_id']"); ?>
                                    </div>
                                </div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::center content-->

            <!--begin::paymet processing-->
            <div 
                class="modal fade modal-iot" 
                id="modal-notification-payment" 
                data-backdrop="static" 
                tabindex="-1" 
                role="dialog" 
                aria-labelledby="mySmallModalLabel" 
                style=""
            >
                <div class="modal-dialog modal-auto">
                    <div class="modal-detail"> 
                        <img src="<?php echo site_url().($this->dist_url); ?>/img/loading.gif" alt="loading..." />
                        <h3 class="modal-caption">
                            Please wait, <br> We are processing your purchase request
                        </h3>
                        <p class="modal-des">
                            Please do not refresh the page to avoid double charges!
                        </p>
                        <div class="modal-action">
                            <button data-dismiss="modal" class="cancel-ajax-cart">Cancel</button>
                        </div>
                    </div>
                    <div class="modal-detail error_detail d-none"> 
                        
                    </div>
                </div>
            </div>
            <!--end::paymet processing-->

        <!--begin::footer-->
        <?php echo $this->footer(); ?>
        <!--end::footer-->
        </body>
        </html>
        <?php return ob_get_clean();
    }


    /**
     * @category Result & Review
     */
    public function result_user_content_area() {
        $panels_html = '';
        if ( count( $this->sections ) > 0) {
            
            foreach ($this->sections as $skey => $section) {
                # main section object
                $section = (object) $section;
                $title   = (string) $section->title;
                $order   = $section->order;
                $content = (string) $section->content;
                $sample  = isset($section->sample)? (object) $section->sample : null;
                $section_id = $section->_id;


                
                /**
                 * Result logic - to check if user has given the response or not
                 */
                $fill_answer = false;
                $user_answer = '';
                if( $this->result ) {
                    $response           = (object) $this->user_data;
                    $quiz_details       = isset($response->quiz)? (object) $response->quiz : null;
                    $user_answers       = isset($response->answers)? (array) $response->answers : null;
                    
                    $current_question   = $this->findObjectByKeyValue($user_answers, 'id', $section_id);

                    if( $current_question ) {
                        $input_type     = isset($current_question->input_type)? $current_question->input_type : '';
                        $q_type         = isset($current_question->q_type)? $current_question->q_type : ''; 
                        $input_class    = isset($current_question->class)? $current_question->class : ''; 
                        $q_total        = isset($current_question->total)? (int) $current_question->total : 0;
                        if( $input_type == "textarea" && $q_type == 'writing-essay' && $input_class == "-checked" ) {
                            $fill_answer = true;
                            $user_answer = isset($current_question->answer)? $current_question->answer : '';
                            
                        }  
                    }
                }

                # also check here - is this task reviewed?
                $review_response        = (object) $this->check_task_for_review($section, $user_answer);
                $review_panel_title     = $review_response->review_panel_title;
                $review_panel_badge     = $review_response->review_panel_badge;
                $review_toggle          = $review_response->review_panel_toggle;
                $review_panel_content   = $review_response->review_panel_content;
                $user_correction        = $review_response->correction;
                $review_user_panel      = $review_response->review_user_panel;



                ob_start(); ?>
                <!--begin::task-->
                <div class="panel-group question-part -part-<?php echo $skey+1; ?>" id="item-arcodion-task-<?php echo $skey+1; ?>">
                    <h2 class="question-part__title"> 
                        <span class="question-part__task-no"> Task <?php echo $skey+1; ?></span>
                    </h2>

                    <!--begin::question panel-->
                    <div class="panel panel-default test-question">
                        <div class="panel-heading">
                            <h4 class="panel-title clearfix"> 
                                <a 
                                    class="test-question__collapse clearfix collapsed" 
                                    aria-expanded="false" 
                                    data-toggle="collapse" 
                                    data-parent="#item-arcodion-part-<?php echo $skey+1; ?>" 
                                    href="#arcodion-<?php echo $skey+1; ?>-item-1">
                                    Question
                                </a>
                            </h4>
                        </div>
                        <div id="arcodion-<?php echo $skey+1; ?>-item-1" class="panel-collapse collapse" style="height: 0px;" aria-expanded="false">
                            <div class="panel-body">
                                <div class="test-question__question"><?php echo $content; ?></div>
                            </div>
                        </div>
                    </div>
                    <!--end::question panel-->

                    <!--begin::answer & Correction panel-->
                    <div class="panel panel-default answer">
                        <div class="panel-heading">
                            <h4 class="panel-title clearfix"> 
                                <a 
                                    class="answer__title clearfix collapsed " 
                                    aria-expanded="false" 
                                    data-toggle="<?php echo $this->review? 'sp-collapse' : 'collapse'; ?>" 
                                    data-item-key="<?php echo $skey+1; ?>"
                                    data-parent="#item-arcodion-part-<?php echo $skey+1; ?>" 
                                    href="#arcodion-<?php echo $skey+1; ?>-item-2"
                                    data-common-panel="task-<?php echo $skey+1; ?>"
                                >
                                    <div class="d-flex flex-row align-items-center justify-content-between">
                                        <span>
                                            <?php echo $user_correction? 'Answer & Correction' : 'Answer'; ?>
                                        </span> 
                                        <small style="margin-right: 35px;">
                                            Words: 
                                            <span class="badge badge-warning">
                                                <?php echo $this->countWords($user_answer); ?> 
                                            </span>
                                        </small>
                                    </div>
                                </a>
                            </h4>
                        </div>
                        <div data-common-panel-content="task-<?php echo $skey+1; ?>" id="arcodion-<?php echo $skey+1; ?>-item-2" class="panel-collapse collapse" style="height: 0px;" aria-expanded="false">
                            <?php if($fill_answer){ ?>
                                <?php if($user_correction) {  ?>
                                    <div class="panel-body -no-padding">
                                        <?php echo $review_user_panel; ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="panel-body">
                                        <div class="answer white-space__pre-wrap"><?php echo $user_answer; ?></div>
                                    </div>
                                <?php } ?>
                            <?php } ?>

                            <?php if(!$fill_answer){ ?>    
                                    <div class="no-answer">
                                        <img src="<?php echo site_url(); ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon-add-answer.svg" alt="" class="no-answer__img">
                                        <h5 class="no-answer__title">Want to add answer for this task?</h5>
                                        <p class="no-answer__desc"> 
                                            It looks like you haven’t finished <strong>Task <?php echo $order; ?></strong> essay from <strong><?php echo $this->quiz->title; ?></strong>. 
                                            If you want to complete Task <?php echo $order; ?> and get evaluation for both tasks, you can retake the test and submit again.
                                        </p>
                                    </div>
                            <?php } ?>
                        </div>
                    </div>
                    <!--end::answer & Correction panel-->

                    <!--begin::Evaluation or Review panel-->
                    <div class="panel panel-default evaluation">
                        <div class="panel-heading">
                            <h4 class="panel-title clearfix"> 
                                
                                <?php if($review_toggle){ ?><a class="evaluation__title clearfix collapsed" aria-expanded="false" data-toggle="collapse" href="#evaluation-panel-<?php echo $skey+1; ?>">
                                    <?php echo $review_panel_title; ?>
                                    <?php echo $review_panel_badge; ?>
                                </a><?php } ?>
                                <?php if(!$review_toggle){ ?><a class="evaluation__title clearfix no-arrow">
                                    <?php echo $review_panel_title; ?>
                                    <?php echo $review_panel_badge; ?>
                                </a><?php } ?>
                            </h4>
                        </div>
                        <?php if($review_toggle){ ?><div id="evaluation-panel-<?php echo $skey+1; ?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                            <div class="panel-body">
                                <?php echo $review_panel_content; ?>
                            </div>
                        </div><?php } ?>
                    </div>
                    <!--end::Evaluation or Review panel-->               



                    <!--begin::sample answer panel-->
                    <?php if($sample !== null){ 
                        $status         = isset($sample->status)? $sample->status : null;
                        $sampleAnswer   = isset($sample->content)? $sample->content : '';
                        if( $status == "true" ) { ?>
                            <div class="panel panel-default sample-answer">
                                <div class="panel-heading">
                                    <h4 class="panel-title clearfix"> 
                                        <a 
                                            class="answer__title clearfix collapsed" 
                                            aria-expanded="false" 
                                            data-toggle="collapse" 
                                            data-parent="#item-arcodion-part-<?php echo $skey+1; ?>" 
                                            href="#arcodion-<?php echo $skey+1; ?>-item-3"
                                        >
                                            Sample Answer
                                        </a>
                                    </h4>
                                </div>
                                <div id="arcodion-<?php echo $skey+1; ?>-item-3" class="panel-collapse collapse" style="height: 0px;" aria-expanded="false">
                                    <div class="panel-body"><?php echo $sampleAnswer; ?></div>
                                </div>
                            </div>  
                        <?php } ?>
                    <?php } ?>  
                    <!--end::sample answer panel-->

                </div>
                <!--end::task-->
                <?php $panels_html .= ob_get_clean();
            }
        }

        
        $dynamic_classes = 'col-xs-12 col-sm-12 col-md-8';
        # condition 1 - if both tasked are reviewd then fullwidth

        // teacher
        if( $this->review ) {
            $dynamic_classes = "col-xs-12 col-sm-12 col-md-12";
        }

        // student
        if( !$this->review ) {
            if( $this->IS_ALL_TASKS_PAID || $this->IS_ALL_TASKS_REVIEWED ) {
                $dynamic_classes = "col-xs-12 col-sm-12 col-md-12";
            }
        }


        ob_start(); ?>
        <div class="<?php echo $dynamic_classes; ?>">

        <!--begin::review overall report--> 

        <!--end::review overall report--> 


        <!--begin::sections [question, answer, review] -->
        <?php echo $panels_html; ?>
        <!--end::sections [question, answer, review] --> 
        </div>
        <?php 
        return ob_get_clean();
    }

    /**
     * @category review sidebar area
     */
    public function result_sidebar_area() {
        # if test is open as student, show conditions
        # if review given - then nothing to show
        # if review not given or plan not paid then show plans
        if( !$this->review && !$this->IS_ALL_TASKS_REVIEWED ){ 
            if(1 == 1) {
                if( $this->IS_ALL_TASKS_PAID ){
                    echo '';
                } else {

                    ob_start(); ?>
                    <!--begin::sidebar for plans-->
                    <div class="col-md-4 col-xs-12 col-sm-12 col-right">
                        <?php echo $this->get_plans_layout(); ?> 
                    </div>
                    <!--end::sidebar for plans-->
                    <?php return ob_get_clean(); 
                }
            }

        }

        if( $this->review ){ ob_start(); ?>
            <!--begin::sidebar for review tabs-->
            <!-- <div class="col-md-2 col-xs-12 col-sm-12 col-right">
                review tabs for evaluation
            </div> -->
            <!--end::sidebar for plans-->
            <?php return ob_get_clean(); 
        }    

    }

    

    public function check_task_for_review( $section, $user_answer ) {
        global $wpdb, $table_proqyz_quiz_review, $table_proqyz_quiz_review_meta;

        # constants
        $section                        = (object) $section;
        $student_id                     = $this->_result->user_id;
        $result_id                      = $this->result_id;
        $section_id                     = $section->_id;
        $section_order                  = $section->order;

        # variables 
        $review_panel_title             = '';
        $review_panel_badge             = '';
        $review_panel_toggle            = true;
        $review_panel_content           = '';
        $review_user_panel              = '';
        $user_correction                = false;

        $score_type = [];
        if( $section_order == 1 ){
            $score_type = array(
                array(
                    "order" => 1,
                    "title" => "Task Response",
                    "data"  => $this->Task_1_response,
                    "prefix" => "score-1"
                ),   
                array(
                    "order" => 2,
                    "title" => "Coherence and cohesion",
                    "data"  => $this->coherence_lines,
                    "prefix" => "score-2"
                ),
                array(
                    "order" => 3,
                    "title" => "Lexical Resource",
                    "data"  => $this->Lexical_lines,
                    "prefix" => "score-3"
                ),
                array(
                    "order" => 4,
                    "title" => "Grammatical Range and Accuracy",
                    "data"  => $this->grammatical_lines,
                    "prefix" => "score-4"
                )
            );
        } else if($section_order == 2){
            $score_type = array(
                array(
                    "order" => 1,
                    "title" => "Task Response",
                    "data"  => $this->Task_2_response,
                    "prefix" => "score-1"
                ),   
                array(
                    "order" => 2,
                    "title" => "Coherence and cohesion",
                    "data"  => $this->coherence_lines,
                    "prefix" => "score-2"
                ),
                array(
                    "order" => 3,
                    "title" => "Lexical Resource",
                    "data"  => $this->Lexical_lines,
                    "prefix" => "score-3"
                ),
                array(
                    "order" => 4,
                    "title" => "Grammatical Range and Accuracy",
                    "data"  => $this->grammatical_lines,
                    "prefix" => "score-4"
                )
            );
        }else{
            $score_type = [];
        }  


        $get_review  = $wpdb->get_row("SELECT `ID` FROM {$table_proqyz_quiz_review} WHERE `result_id` = '$result_id' AND `user_id` = '$student_id' AND `status` = 1 AND `section_id` = '$section_id' LIMIT 1");
        
        # evaluated
        if( $get_review ) {
            $review_meta_id = $get_review->ID;
            # get review metadata
            
            # 0.) reviewer - check response as [ai or teacher] 
            $reviewer_id = '-1';
            $reviewer_id_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 'reviewer' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
            if( $reviewer_id_key ) { 
                $reviewer_id = (int) $reviewer_id_key->meta_value;
            }

            # [optional]: get review log
            $review_log = (object) [];
            $review_log_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 'review-log' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
            if( $review_log_key ) { 
                $review_log = (object) maybe_unserialize($review_log_key->meta_value);
            }


            $review_status = 'queue';
            $review_status_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 'review-status' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
            if( $review_status_key ) { 
                $review_status = $review_status_key->meta_value;
            }




            # panel content generator
            ob_start();

                # teacher side
                if( $this->review ) {
                    
                    # by Evaluator
                    if( $reviewer_id != "-1" ) {
                        # review meta content
                        echo $this->review_panel_for_teachers([
                            "section_id"        => $section_id,
                            "section_order"     => $section_order,
                            "result_id"         => $result_id,
                            "student_id"        => $student_id
                        ]);
                    }

                    # by AI
                    if( $reviewer_id == "-1" ) {
                        #  display ai review content - mode: readonly
                        // echo "display ai review but not editable";
                        # display mode evaluated by ai for student - view only mode
                        $final_ai_response = (object) $this->display_evaluated_report_for_student_by_ai([
                            "section_id"        => $section_id,
                            "section_order"     => $section_order,
                            "result_id"         => $result_id,
                            "student_id"        => $student_id,
                            "review_id"         => $review_meta_id,
                            "user_answer"       => $user_answer
                        ]);

                        $user_correction = $final_ai_response->correction;
                        $review_user_panel = $final_ai_response->review_user_panel;

                        echo $final_ai_response->evaluation;

                    }
                }

                # student side
                if( !$this->review ) {

                    # by teacher
                    if( $reviewer_id != "-1" ) {
                        # display mode evaluated by teacher for student - view only mode
                        echo $this->display_evaluated_report_for_student_by_evaluator([
                            "section_id"        => $section_id,
                            "section_order"     => $section_order,
                            "result_id"         => $result_id,
                            "student_id"        => $student_id
                        ]);
                    }

                    # by ai 
                    if( $reviewer_id == "-1" ) {
                        # display mode evaluated by ai for student - view only mode
                        $final_ai_response = (object) $this->display_evaluated_report_for_student_by_ai([
                            "section_id"        => $section_id,
                            "section_order"     => $section_order,
                            "result_id"         => $result_id,
                            "student_id"        => $student_id,
                            "review_id"         => $review_meta_id,
                            "user_answer"       => $user_answer
                        ]);

                        $user_correction = $final_ai_response->correction;
                        $review_user_panel = $final_ai_response->review_user_panel;

                        echo $final_ai_response->evaluation;
                    }


                }
                
            $review_panel_content = ob_get_clean();
            


            # panel badge generator
            ob_start();

                # teacher side
                if( $this->review ) {

                    # for task 1
                    if($section_order == 1 || $section_order == 2){
                        # task 1 paid - review by AI

                        if( array_key_exists($section_id, (array) $this->enrolled_for) ){

                            # by AI
                            if ($reviewer_id == "-1" ) {
                                if ( $review_status == "queue" ) { ?>
                                    <span class="badge badge-info -writing" style="position: absolute;right: 65px;top: 20px;">
                                        In Queue
                                    </span>
                                <?php } else if ( $review_status == "processing" ) { ?>
                                    <span class="badge badge-info -writing" style="position: absolute;right: 65px;top: 20px;">
                                        Processing
                                    </span>
                                <?php } else if( $review_status == "completed" ) { ?>
                                    <span class="badge badge-success -writing" style="position: absolute;right: 65px;top: 20px;">
                                        Evaluated by AI
                                    </span>
                                <?php } else if( $review_status == "failed" ) { ?>
                                    <span class="badge badge-danger -writing" style="position: absolute;right: 65px;top: 20px;">
                                        Failed to Evaluate by Ai
                                    </span>
                                <?php }
                            }

                            # by evaluator
                            if ($reviewer_id != "-1" ) { ?>
                                <span class="badge badge-success -writing" style="position: absolute;right: 65px;top: 20px;">
                                    Evaluated
                                </span>
                            <?php }
                        }
                            
                        
                        
                        # if task 1 is not paid
                        if( !array_key_exists($section_id, (array) $this->enrolled_for) ) {
                            # task 1 not paid but evaluated
                            # by AI
                            if ($reviewer_id == "-1" ) {
                                if ( $review_status == "queue" ) { ?>
                                    <span class="badge badge-info -writing" style="position: absolute;right: 65px;top: 20px;">
                                        In Queue (Not Enrolled)
                                    </span>
                                <?php } else if ( $review_status == "processing" ) { ?>
                                    <span class="badge badge-info -writing" style="position: absolute;right: 65px;top: 20px;">
                                        Processing (Not Enrolled)
                                    </span>
                                <?php } else if( $review_status == "completed" ) { ?>
                                    <span class="badge badge-success -writing" style="position: absolute;right: 65px;top: 20px;">
                                        Evaluated by AI (Not Enrolled)
                                    </span>
                                <?php } else if( $review_status == "failed" ) { ?>
                                    <span class="badge badge-danger -writing" style="position: absolute;right: 65px;top: 20px;">
                                        Failed to Evaluate by AI (Not Enrolled)
                                    </span>
                                <?php }
                            }

                            # by evaluator
                            if ($reviewer_id != "-1" ) { ?>
                                <span class="badge badge-success -writing" style="position: absolute;right: 65px;top: 20px;">
                                    Evaluated (Not Enrolled) 
                                </span>
                            <?php }
                        }

                        
                    }
                }

                # student side
                if( !$this->review ) {
                    # badge for panel
                    
                    # for task 1
                    if( $section_order == 1 || $section_order == 2){
                        # task 1 paid or unpaid - but yet evaluated/ reviewed
                        if( array_key_exists($section_id, (array) $this->enrolled_for) || !array_key_exists($section_id, (array) $this->enrolled_for)){  

                            # by AI
                            if ($reviewer_id == "-1" ) {
                                if ( $review_status == "queue" ) { ?>
                                    <span class="badge badge-info -writing" style="position: absolute;right: 65px;top: 20px;">
                                        In Queue
                                    </span>
                                <?php } else if ( $review_status == "processing" ) { ?>
                                    <span class="badge badge-info -writing" style="position: absolute;right: 65px;top: 20px;">
                                        Processing
                                    </span>
                                <?php } else if( $review_status == "completed" ) { ?>
                                    <span class="badge badge-success -writing" style="position: absolute;right: 65px;top: 20px;">
                                        Evaluated by AI
                                    </span>
                                <?php } else if( $review_status == "failed" ) { ?>
                                    <span class="badge badge-danger -writing" style="position: absolute;right: 65px;top: 20px;">
                                        Failed to Evaluate by AI
                                    </span>
                                <?php }
                            }


                            # by evaluator
                            if ($reviewer_id != "-1" ) { ?>
                                <span class="badge badge-success -writing" style="position: absolute;right: 65px;top: 20px;">
                                    Evaluated
                                </span>
                            <?php } ?>

                            
                        <?php }
                    }

                }
            $review_panel_badge = ob_get_clean();

            # panel title generator
            $review_panel_title = 'Evaluation';
            # final return when review is given
        } 
        
        # under evaluation - merged ----------------------
        if( !$get_review ){
            $un_id = date("U");
            # panel content generator
            
            ob_start();
                # teacher side - under review
                if( $this->review ) {
                    # manage panel to give review to result for the first time if review is not given
                    # for task 1
                    if( $section_order == 1 || $section_order == 2){
                        # task 1 paid - for AI
                        $is_paid        = false;
                        $enrolled_as    = null;
                        $show_anyway    = false;

                        if( array_key_exists($section_id, (array) $this->enrolled_for)){ 
                            $is_paid = true;
                        }


                        # if its paid then check what kind of enrollment is that
                        if( $is_paid ) {
                            $enrolled_section = (object) $this->enrolled_for[$section_id];
                            $enrolled_as = $enrolled_section->payment_for;
                        }

                            
                        if( $is_paid ) { $show_anyway = true; ?>
                            <?php if($enrolled_as == "ai" ) { ?>  
                                <div class="alert alert-success">Task is enrolled for AI Evaluation</div>
                            <?php } else { ?>
                                <div class="alert alert-success">Task is enrolled for Instructor Evaluation</div>
                            <?php } ?>
                            
                        <?php }

                        if( !$is_paid ) {
                            $show_anyway = true;
                            if( $this->evaluation_reason == "wp-evaluation-disabled") { ?>
                                <div class="alert alert-info d-flex flex-row align-items-center justify-content-between">
                                    <p>Task evaluation is disabled (wp-admin)</p>
                                    <button class="btn btn-warning evaluate__anyway-btn" data-toggle="toggle-visibility" data-target="#evaluate__anyway-<?php echo $section_id; ?>" type="button">Evaluate anyway</button>
                                </div>
                            <?php } else if( $this->evaluation_reason == "proqyz-evaluation-disabled"  ) { ?>
                                <div class="alert alert-success d-flex flex-row align-items-center justify-content-between">
                                    <p>Task is enrolled for evaluation</p>
                                </div>
                            <?php } else if( $this->evaluation_reason == "wp-evaluation-no-cost") { ?>
                                <div class="alert alert-info d-flex flex-row align-items-center justify-content-between">
                                    <p>Task evaluation Pricing not set</p>
                                    <button class="btn btn-warning evaluate__anyway-btn" data-toggle="toggle-visibility" data-target="#evaluate__anyway-<?php echo $section_id; ?>" type="button">Evaluate anyway</button>
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-warning d-flex flex-row align-items-center justify-content-between">
                                    <p>Task is not enrolled</p>
                                    <button class="btn btn-warning evaluate__anyway-btn" data-toggle="toggle-visibility" data-target="#evaluate__anyway-<?php echo $section_id; ?>" type="button">Evaluate anyway</button>
                                </div>
                            <?php }
                        }

                        

                
                        # task 1 paid or not - for Evaluator ?>
                        <div id="evaluate__anyway-<?php echo $section_id; ?>" class="evaluate__anyway <?php echo ($show_anyway)? '--show-contents' : ''; ?>" style="display:none;">

                            <ul class="nav nav-tabs" role="tablist">
                                
                                <li data-evaluator-category="evaluator" data-ajax-review="1" role="presentation" class="active"> 
                                    <button class="my-purchase__btn -completed" role="tab">
                                        Teacher
                                    </button>
                                </li>
                                <li data-evaluator-category="ai" data-ajax-review="0" role="presentation" class=""> 
                                    <button class="my-purchase__btn -inprogress" role="tab">
                                        AI
                                    </button>
                                </li>
                            </ul>


                            <div id="ai-tab" class="tab__content " style="display:none;">
                                <form class="generate-ai-evaluation__as-teacher">
                                    
                                    <div class="no-answer" >
                                        <!--img src="https://cdn.ieltslms.com/cdn/ielts/assets/img/robot-avatar.png" alt="" class="no-answer__img" style="background: white;border-radius: 100px;"-->
                                        <img src="/wp-content/plugins/spacetree/libs/proqyz/includes/public/dist/img/IELTS-GPT.webp" alt="" class="no-answer__img" style="background: white;border-radius: 100px;width: 100px;margin-bottom: 0px;border: 1px solid #f0ad4e52;">
                                        <h5 class="no-answer__title">Evaluate with AI</h5>
                                        <p>Get your band score instantly using AI.</p>
                                        <div class="notice__area"></div>
                                        <input type="hidden" name="result_id" value="<?php echo $result_id; ?>" />
                                        <input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
                                        <input type="hidden" name="section_order" value="1" />
                                        <button type="submit" class="iot-bt no-answer__btn">
                                            Generate now
                                        </button>
                                        
                                    </div>
                                </form>

                            </div>

                            
                            <div id="evaluator-tab" class="tab__content --show-contents" style="display:none;">
                                <?php 
                                    echo $this->review_panel_for_teachers([
                                        "section_id"        => $section_id,
                                        "section_order"     => $section_order,
                                        "result_id"         => $result_id,
                                        "student_id"        => $student_id
                                    ]);
                                ?>
                            </div>


                        </div>
                        <?php 
                            


                    }
                }

                # student side - under review
                if( !$this->review ) { 
                    
                    # for task 1
                    if($section_order == 1 || $section_order == 2){
                        # task 1 paid but not evaluated
                        
                        
                        if( array_key_exists($section_id, (array) $this->enrolled_for )){ 
                            $enrolled_section = (object) $this->enrolled_for[$section_id];
                            if( $enrolled_section->payment_for === "ai" ) { ?>
                                <div class="no-answer">
                                    <!--img src="https://cdn.ieltslms.com/cdn/ielts/assets/img/robot-avatar.png" alt="" class="no-answer__img" style="background: white;border-radius: 100px;"-->
                                    <img src="/wp-content/plugins/spacetree/libs/proqyz/includes/public/dist/img/IELTS-GPT.webp" alt="" class="no-answer__img" style="background: white;border-radius: 100px;width: 100px;margin-bottom: 0px;border: 1px solid #f0ad4e52;">
                                    <h5 class="no-answer__title">Generate Evaluation</h5>
                                    <p>Get your band score instantly using AI.</p>
                                    <form class="generate-ai-evaluation">
                                        <div class="notice__area"></div>
                                        <input type="hidden" name="result_id" value="<?php echo $result_id; ?>" />
                                        <input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
                                        <input type="hidden" name="section_order" value="1" />
                                        <button type="submit" class="iot-bt no-answer__btn">
                                            Generate now
                                        </button>
                                    </form>
                                </div>
                            <?php } else { ?>
                                <div class="no-answer">
                                    <img src="https://cdn.ieltslms.com/cdn/ielts/assets/img/icon-add-answer.svg" alt="" class="no-answer__img">
                                    <h5 class="no-answer__title">Task is Under Evaluation</h5>
                                    <p>Your task is under evaluation. You will be informed by email when the evaluation is completed.</p>
                                </div>
                            <?php } ?>
                        <?php } 

                        # task 1 not paid and not evaluated
                        if( !array_key_exists($section_id, (array) $this->enrolled_for )){
                            if($this->evaluation_reason == "wp-evaluation-disabled" || $this->evaluation_reason == "proqyz-evaluation-disabled" || $this->evaluation_reason == "wp-evaluation-no-cost") { ?> 
                                <div class="no-answer">
                                    <img src="https://cdn.ieltslms.com/cdn/ielts/assets/img/icon-add-answer.svg" alt="" class="no-answer__img">
                                    <h5 class="no-answer__title">Task is Under Evaluation</h5>
                                    <p>Your task is under evaluation. You will be informed by email when the evaluation is completed.</p>
                                </div>
                            <?php } else { ?> 
                                <div class="no-answer">
                                    <img src="https://cdn.ieltslms.com/cdn/ielts/assets/img/icon-add-answer.svg" alt="" class="no-answer__img">
                                    <h5 class="no-answer__title">You are not enrolled for this task.</h5>
                                    <p>Please purchase an enrollment to access and review your task.</p>
                                </div>    
                            <?php } ?>
                        <?php }
                    }
                    
                }
            $review_panel_content = ob_get_clean();
            

            # panel badge generator
            ob_start();
                # teacher side - under review
                if( $this->review ) {

                    # for task 1
                    if($section_order == 1 || $section_order == 2){
                        # task 1 paid
                        if( array_key_exists($section_id, (array) $this->enrolled_for) ){ 
                            # if task 1 is not reviewed
                            ?>
                            <span class="badge badge-warning -writing" style="position: absolute;right: 65px;top: 20px;">
                                Under Evaluation
                            </span>
                            <?php 
                        }
                        
                        # if task 1 is not paid
                        if( !array_key_exists($section_id, (array) $this->enrolled_for)) {
                            if( $this->evaluation_reason === 'proqyz-evaluation-disabled'){ ?>
                                <span class="badge badge-warning -writing" style="position: absolute;right: 65px;top: 20px;">
                                    Under evaluation
                                </span>
                            <?php } else if( $this->evaluation_reason === "wp-evaluation-disabled") { ?>
                                <span class="badge badge-warning -writing" style="position: absolute;right: 65px;top: 20px;">
                                    Under evaluation
                                </span>
                            <?php } else if( $this->evaluation_reason === "wp-evaluation-no-cost") { ?>
                                <span class="badge badge-warning -writing" style="position: absolute;right: 65px;top: 20px;">
                                    Under evaluation
                                </span>
                            <?php } else { ?>
                            <span class="badge badge-warning -writing" style="position: absolute;right: 65px;top: 20px;">
                                Not Enrolled
                            </span>
                            <?php }
                            # task 1 not paid but not evaluated
                            
                        }

                        
                    }
                    
                }

                # student side - under review
                if( !$this->review ) {
                    # badge for panel
                    
                    # for task 1
                    if($section_order == 1 || $section_order == 2){
                        # task 1 paid
                        if( array_key_exists($section_id, (array) $this->enrolled_for) ){ 
                            # if task 1 is not reviewed
                            ?>
                            <span class="badge badge-warning -writing" style="position: absolute;right: 65px;top: 20px;">
                                Under Evaluation
                            </span>
                            <?php 
                        }
                        
                        # if task 1 is not paid
                        if( !array_key_exists($section_id, (array) $this->enrolled_for)) {
                            if( $this->evaluation_reason === 'proqyz-evaluation-disabled'){ ?>
                                <span class="badge badge-warning -writing" style="position: absolute;right: 65px;top: 20px;">
                                    Under evaluation
                                </span>
                            <?php } else if( $this->evaluation_reason === "wp-evaluation-disabled") { ?>
                                <span class="badge badge-warning -writing" style="position: absolute;right: 65px;top: 20px;">
                                    Under evaluation
                                </span>
                            <?php } else if( $this->evaluation_reason === "wp-evaluation-no-cost") { ?>
                                <span class="badge badge-warning -writing" style="position: absolute;right: 65px;top: 20px;">
                                    Under evaluation
                                </span>
                            <?php } else { ?>
                            <span class="badge badge-warning -writing" style="position: absolute;right: 65px;top: 20px;">
                                Get Evaluation
                            </span>
                            <?php }
                            # task 1 not paid but not evaluated
                            
                        }

                        
                    }

                }
            $review_panel_badge = ob_get_clean();

            # final return when review is not given
            $review_panel_title = 'Evaluation';
        }


        return (object) [
            "review_panel_title"    => $review_panel_title,
            "review_panel_badge"    => $review_panel_badge,
            "review_panel_toggle"   => $review_panel_toggle,
            "review_panel_content"  => $review_panel_content,
            "review_user_panel"     => $review_user_panel,
            "correction"            => $user_correction
        ];
    }

    /**
     * @var given_review_by_teacher_panel
     */
    public function review_panel_for_teachers($params = []) {
        global $wpdb, $table_proqyz_quiz_review, $table_proqyz_quiz_review_meta;
        $params = (object) $params;
        $section_id     = $params->section_id;
        $section_order  = $params->section_order;
        $student_id     = $params->student_id;
        $result_id       = $params->result_id;

        # default data for review
        $review_feedback_status = 1;
        $review_feedback_content = "";
        $review_scores = [1,1,1,1];

        $get_review  = $wpdb->get_row("SELECT `ID` FROM {$table_proqyz_quiz_review} WHERE `result_id` = '$result_id' AND `user_id` = '$student_id' AND `status` = 1 AND `section_id` = '$section_id' LIMIT 1");
        # evaluated
        if( $get_review ) {
            $review_meta_id = $get_review->ID;

            # 1.) review content
            $review_feedback_content_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 'review-feedback-content' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
            if( $review_feedback_content_key ) { $review_feedback_content = stripslashes($review_feedback_content_key->meta_value); }
            

            # 2.) review scores
            $review_scores_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 'review-scores' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
            if( $review_scores_key ) { 
                $review_scores = (array) json_decode($review_scores_key->meta_value); 
            }
        }
        

        ob_start(); ?>
        <form class="proqyz__post-evaluation-form" style="display:contents;">
            <div class="row">
                <!--begin::overall feedback -->
                <div class="col-xs-12 col-sm-12 mb-2">
                    <div class="form-group evaluation__item-card"> 
                        <label for="textarea" class="evaluation__item-title">Overall Feedback</label>
                        <div class="evaluation__score" style="display:none;"></div>
                        <div class="form-control evaluation__item-comment --review" disabled="disabled" style="padding:0;height:100%;">
                            <div style="max-height: 100%; overflow: auto">
                                <textarea id="feedback-content" name="feedback-content" class="summernote"><?php echo $review_feedback_content; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::overall feedback -->

                <!-- begin::Task Achivenment -->
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group evaluation__item-card"> 
                        <label for="textarea" class="evaluation__item-title">Task Achievement</label>
                        <div class="evaluation__score" style="display:none;">
                            <!--begin::band score selector--> 
                            <!--end::band score selector-->
                        </div>
                        <div class="form-control evaluation__item-comment --review" disabled="disabled" style="height:100%;">
                            <div style="max-height: 100%; overflow: auto">
                                <select id="score-1" name="score-1" class="iot-opselect band-scored form-select form-control">
                                    <?php foreach( array_reverse($this->Task_1_response, true) as $tindex => $tkey  ) { ?>
                                        <option <?php echo (int) $review_scores[0] == (int) $tindex? 'selected' : ''; ?> value="<?php echo $tindex; ?>"><?php echo $tindex; ?> - <?php echo $tkey; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end::Task Achivenment -->

                <!-- begin::Coherence and Cohesion -->
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group evaluation__item-card"> 
                        <label for="textarea" class="evaluation__item-title">Coherence and Cohesion</label>
                        <div class="evaluation__score" style="display:none;">
                            <!--begin::band score selector--> 
                            <!--end::band score selector-->
                        </div>
                        <div class="form-control evaluation__item-comment --review" disabled="disabled" style="height:100%;">
                            <div style="max-height: 100%; overflow: auto">
                                <select id="score-2" name="score-2" class="iot-opselect band-scored form-select form-control">
                                    <?php foreach( array_reverse($this->coherence_lines, true) as $cindex => $ckey  ) { ?>
                                        <option <?php echo (int) $review_scores[1] == (int) $cindex? 'selected' : ''; ?> value="<?php echo $cindex; ?>"><?php echo $cindex; ?> - <?php echo $ckey; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end::Coherence and Cohesion -->

                <!-- begin::Lexical Resource -->
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group evaluation__item-card"> 
                        <label for="textarea" class="evaluation__item-title">Lexical Resource</label>
                        <div class="evaluation__score" style="display:none;">
                            <!--begin::band score selector--> 
                            <!--end::band score selector-->
                        </div>
                        <div class="form-control evaluation__item-comment --review" disabled="disabled" style="height:100%;">
                            <div style="max-height: 100%; overflow: auto">
                                <select id="score-3" name="score-3" class="iot-opselect band-scored form-select form-control">
                                    <?php foreach( array_reverse($this->Lexical_lines, true) as $lindex => $lkey  ) { ?>
                                        <option <?php echo (int) $review_scores[2] == (int) $lindex? 'selected' : ''; ?> value="<?php echo $lindex; ?>"><?php echo $lindex; ?> - <?php echo $lkey; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end::Lexical Resource -->

                <!-- begin::Grammatical Range and Accuracy -->
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group evaluation__item-card"> 
                        <label for="textarea" class="evaluation__item-title">Grammatical Range and Accuracy</label>
                        <div class="evaluation__score" style="display:none;">
                            <!--begin::band score selector--> 
                            <!--end::band score selector-->
                        </div>
                        <div class="form-control evaluation__item-comment --review" disabled="disabled" style="height:100%;">
                            <div style="max-height: 100%; overflow: auto">
                                <select id="score-4" name="score-4" class="iot-opselect band-scored form-select form-control">
                                    <?php foreach( array_reverse($this->grammatical_lines, true) as $gindex => $gkey  ) { ?>
                                        <option <?php echo (int) $review_scores[3] == (int) $gindex? 'selected' : ''; ?> value="<?php echo $gindex; ?>"><?php echo $gindex; ?> - <?php echo $gkey; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>                        
                <!-- end::Grammatical Range and Accuracy --> 

                <!-- begin::Footer -->
                <div class="col-xs-12 col-sm-12">
                    <div class="form-group evaluation__item-card">
                        <div class="form-group evaluation__item-card--footer">
                            <div class="notice__area"></div>
                            <div class="d-flex flex-row align-items-center">
                                <?php if( $get_review ) { ?>
                                    <button type="button" class="btn btn-danger review__remove" style="margin-right:4px;">Delete Review</button>
                                <?php } ?>
                                <button type="submit" class="btn btn-primary">Post Review</button>   
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-none" style="display:none;">
                    <?php if( $get_review ) { ?>
                        <input type="hidden" id="review-id" name="review-id" value="<?php echo $get_review->ID; ?>" />                
                    <?php } ?>
                    <input type="hidden" id="section-id" name="section-id" value="<?php echo $section_id; ?>" />
                    <input type="hidden" id="section-order" name="section-order" value="<?php echo $section_order; ?>" /> 
                    <input type="hidden" id="result-id" name="result-id" value="<?php echo $result_id; ?>" />                       
                </div>
                <!-- begin::Footer -->

            </div>
        </form>
        <?php return ob_get_clean();

    }

    /**
     * @var display_evaluated_report_for_student_by_ai
     */
    public function display_evaluated_report_for_student_by_ai($params = []) {
        global $wpdb, $table_proqyz_quiz_review_meta;
        $params = (object) $params;
        $section_id     = $params->section_id;
        $section_order  = $params->section_order;
        $student_id     = $params->student_id;
        $result_id      = $params->result_id;
        $review_meta_id = $params->review_id;
        $user_answer    = $params->user_answer;
        $review_panel_content = "";
        $extra_content = '';
        $review_user_panel = '';
        $user_correction = false;
        $review_log      = null;


        # check evaluation status
        $review_status = 'queue';
        $review_status_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 'review-status' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
        if( $review_status_key ) { 
            $review_status = $review_status_key->meta_value;
        }

        if( $review_status == "failed" ) {
            $review_log_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 'review-log' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
            if( $review_log_key ) {
                $review_log = (object) maybe_unserialize($review_log_key->meta_value);
            }
        }

        # reason for re-evaluation
        $reason_data = (object) [
            "status" => false,
            "reason" => ""
        ];
        $reason_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 're-evaluation' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
        if( $reason_key ) { 
            $reason_data = (object) maybe_unserialize($reason_key->meta_value);
        }
        
        
        # for Teacher
        if( $this->review ) {
            if( $review_status != "queue" || $review_status != "processing" || $review_status != "started" ) {
                ob_start(); ?>
                <br />
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <form class="proqyz__reset-evaluation">
                            <div class="form-group evaluation__item-card">
                                <div class="notice__area "  style="width:90%;margin:0 auto;"></div>
                                <div class="alert alert-info row" style="width:90%;margin:0 auto;">
                                    <div class="col-xs-12 col-md-10" style="margin-bottom:4px;">
                                        <p style="margin:0;">This option will remove the current AI evaluation, allowing students to review the AI evaluation again.</p>
                                    </div>
                                    <div class="col-xs-12 col-md-2" style="margin-bottom:4px;">
                                        
                                        
                                            <input type="hidden" name="section-id" id="section-id" value="<?php echo $section_id; ?>" />
                                            <input type="hidden" name="result-id" id="result-id" value="<?php echo $result_id; ?>" />
                                            <button class="btn btn-primary btn-sm" type="submit"> Reset Evaluation </button>
                                        
                                    </div>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php if($reason_data->status){ ?>
                        <div class="col-xs-12 col-sm-12">
                            <div class="alert alert-warning" style="width:90%;margin:0 auto;">
                                <p><b>Reason by Student (for Re-Evaluation)</b>: <?php echo $reason_data->reason; ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php $extra_content = ob_get_clean();
            }
        }

        # for students
        if( !$this->review ) {
            if( $review_status == "completed" ) {
                ob_start(); ?>
                <br />
                <div class="row">
                    <div class="col-xs-12 col-sm-12 mb-2">
                        <div class="accordion" id="accordionExample">

                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                            Re-Evaluation Request 
                                        </button>
                                    </h2>
                                </div>

                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                    <div class="card-body">
                                        <div class="col-xs-12 col-md-12">
                                            <form class="proqyz__reevaluation-request" style="width:90%;margin:0 auto;">
                                                <div class="notice__area"></div>
                                                <div class="alert alert-info row" style="margin:0 auto 10px;">
                                                    <p style="margin:0;">Request for <b>Re-Evaluation</b> (When you submit a re-evaluation request, the instructor will review it and remove the current evaluation if needed.)</p>
                                                </div>
                                                <div class="form-group">
                                                    <label for="reason">Reason of Re-Evaluation</label>
                                                    <textarea required class="form-control" id="reason" rows="3" placeholder="Explanation or reason here"><?php echo $reason_data->reason; ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <input type="hidden" name="section-id" id="section-id" value="<?php echo $section_id; ?>" />
                                                    <input type="hidden" name="result-id" id="result-id" value="<?php echo $result_id; ?>" />
                                                    <button class="btn btn-primary btn-sumit"  type="submit">
                                                        <?php echo $reason_data->status? 'Re-Send Request' : 'Send Request'; ?>
                                                    </button>
                                                </div>

                                            </form>
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                    </div>

                    
                </div>
                <?php $extra_content = ob_get_clean();
            }
        }


        if( $review_status == "processing" ) {

            ob_start(); ?>
            <div class="no-answer">
                <img src="https://cdn.ieltslms.com/cdn/ielts/assets/img/icon-add-answer.svg" alt="" class="no-answer__img">
                <h5 class="no-answer__title">Evaluation Processing</h5>
                <p>This may take a few minutes. You can leave this tab, and we’ll notify you by email once the evaluation is complete.</p>
            </div>
            <?php $review_panel_content = ob_get_clean();
        } else if( $review_status == "started" ){
            ob_start(); ?>
            <div class="no-answer">
                <img src="https://cdn.ieltslms.com/cdn/ielts/assets/img/icon-add-answer.svg" alt="" class="no-answer__img">
                <h5 class="no-answer__title">Evaluation Started</h5>
                <p>Task evaluation is started, You can leave this tab, and we’ll notify you by email once the evaluation is complete.</p>
            </div>
            <?php $review_panel_content = ob_get_clean();
        } else if( $review_status == "failed") {
            $log_type               = isset($review_log->type)? $review_log->type : 'error';
            $log_message            = isset($review_log->message)? $review_log->message : 'Processing AI Evaluation stopped due to failure response, below are the details.';
            $log_rest_error         = isset($review_log->error)? (object) $review_log->error : null;
            $log_rest_error_type    = isset($log_rest_error->type)? $log_rest_error->type : 'error';
            $log_rest_error_message = isset($log_rest_error->message)?  $log_rest_error->message : 'Unknown response from Request';

            ob_start(); ?>
            <div class="no-answer">
                <?php if($log_type == "error"){ ?>
                    <img src="https://cdn.ieltslms.com/cdn/ielts/assets/img/icon-add-answer.svg" alt="" class="no-answer__img">
                    <h5 class="no-answer__title">Evaluation Failed</h5>
                    <p><?php echo $log_message; ?></p>
                    
                    <?php if($log_rest_error){ ?>
                        <div class="alert alert-<?php echo $log_rest_error_type; ?> d-flex flex-row align-items-center justify-content-between">
                            <p><?php echo $log_rest_error_message; ?></p>
                            <form class="generate-ai-evaluation">
                                <input type="hidden" name="result_id" value="<?php echo $result_id; ?>" />
                                <input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
                                <input type="hidden" name="section_order" value="2" />
                                <button type="submit" class="btn btn-sm btn-slim btn-danger">
                                    Try again
                                </button>
                            </form>
                            
                        </div>
                    <?php } else { ?>
                        <form class="generate-ai-evaluation">
                            <input type="hidden" name="result_id" value="<?php echo $result_id; ?>" />
                            <input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
                            <input type="hidden" name="section_order" value="2" />
                            <button type="submit" class="iot-bt no-answer__btn">
                                Evaluate Again
                            </button>
                        </form>
                    <?php } ?>
                <?php } ?>
            </div>
            <?php $review_panel_content = ob_get_clean();
        } else if( $review_status == "queue" ) {
            ob_start(); ?>
            <div class="no-answer">
                <img src="https://cdn.ieltslms.com/cdn/ielts/assets/img/icon-add-answer.svg" alt="" class="no-answer__img">
                <h5 class="no-answer__title">Evaluation interrupted</h5>
                <p>Evaluation is not complted, because it was interruped by some reason, please evaluate again.</p>
                <form class="generate-ai-evaluation">
                    <input type="hidden" name="result_id" value="<?php echo $result_id; ?>" />
                    <input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
                    <input type="hidden" name="section_order" value="2" />
                    <button type="submit" class="iot-bt no-answer__btn">
                        Evaluate Again
                    </button>
                </form>                        
            </div>
            <?php $review_panel_content = ob_get_clean();
        } else if( $review_status == "completed") {
            # get review main content
            $review_content_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 'ai-review-content' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
            if( $review_content_key ) { 
                $review_content_json_string = $review_content_key->meta_value;
                $ai_review_content_json = (object) json_decode($review_content_json_string, true);
                if( isset($ai_review_content_json->success) ) {
                    $ai_response = (object) $ai_review_content_json->success;
                    if( isset($ai_response->format) && isset($ai_response->response) ) {
                        # template 1
                        if( $ai_response->format == "json" ) {
                            $evaluation_report = (array) $ai_response->response;
                            $ta = [];
                            $cc = [];
                            $lr = [];
                            $ga = [];
                            $overall = [];

                            if (!empty($evaluation_report)) {
                                // Extract each section from the report
                                if (isset($evaluation_report['task-achievement'])) {
                                    $ta['feedback'] = $evaluation_report['task-achievement']['feedback'];
                                    $ta['band'] = $evaluation_report['task-achievement']['band'];
                                }
                                
                                if (isset($evaluation_report['coherence-and-cohesion'])) {
                                    $cc['feedback'] = $evaluation_report['coherence-and-cohesion']['feedback'];
                                    $cc['band'] = $evaluation_report['coherence-and-cohesion']['band'];
                                }
                            
                                if (isset($evaluation_report['lexical-resource'])) {
                                    $lr['feedback'] = $evaluation_report['lexical-resource']['feedback'];
                                    $lr['band'] = $evaluation_report['lexical-resource']['band'];
                                }
                            
                                if (isset($evaluation_report['grammatical-range-and-accuracy'])) {
                                    $ga['feedback'] = $evaluation_report['grammatical-range-and-accuracy']['feedback'];
                                    $ga['band'] = $evaluation_report['grammatical-range-and-accuracy']['band'];
                                }
                            
                                if (isset($evaluation_report['overall'])) {
                                    $overall['feedback'] = $evaluation_report['overall']['feedback'];
                                    $overall['band'] = $evaluation_report['overall']['band'];
                                }

                                if (isset($evaluation_report['correction'])) {
                                    $overall['correction'] = $evaluation_report['correction'];
                                }

                                if (isset($evaluation_report['highlights'])) {
                                    $overall['highlights'] = $evaluation_report['highlights'];  // Highlights array
                                    if( isset($evaluation_report['user-response']) ) {
                                        $highlights_mistakes = '';
                                        $hightlighted_response = '';
                                        $tmp_resposne = $evaluation_report['user-response'];
                                        if(count($overall['highlights']) > 0 ) {
                                            
                                            ob_start(); 
                                            foreach( $overall['highlights'] as $hkey => $hint ) {
                                                $hint           = (object) $hint;
                                                $index          = $hint->index;
                                                $mistake        = $hint->mistake;
                                                $correction     = $hint->correction;

                                                // Find the span tag with the current index
                                                $pattern = '/<span data-index="' . $index . '">(.*?)<\/span>/';

                                                // Replace it with <del> for the mistake and <ins> for the correction
                                                $replacement = '<del class="-bb-orange" data-index="' . $index . '">' . $mistake . '</del> <span class="correction -bb-blue" data-index="' . $index . '">' . $correction . '</span>';
                                                
                                                // Perform the replacement in the user response
                                                $tmp_resposne = preg_replace($pattern, $replacement, $tmp_resposne);


                                                ?>
                                                <div data-drupal-selector="corrections-word" data-index="<?php echo $hint->index; ?>" class="answer__status-box -orange">
                                                    <div class="answer__status-header"> 
                                                        <strong>deleted</strong>: <span class="correct-text"><?php echo $hint->mistake; ?> </span>
                                                    </div>
                                                    <div class="answer__status-body"><?php echo $hint->reason; ?></div>
                                                </div>
                                                <div data-drupal-selector="corrections-word" data-index="<?php echo $hint->index; ?>" class="answer__status-box -blue">
                                                    <div class="answer__status-header"> 
                                                        <strong>added</strong>: <span class="correct-text"><?php echo $hint->correction; ?> </span>
                                                    </div>
                                                    <div class="answer__status-body">Corrected the spelling of "<?php echo $hint->correction; ?>"</div>
                                                </div>
                                                <?php 
                                            }
                                            $highlights_mistakes = ob_get_clean();

                                            
                                        }
                                        ob_start(); ?>
                                        <div class="row answer__answer-box">
                                            <div class="col-xs-12 col-sm-8 answer__col-left">
                                                <div class="writing-result-corrections" style="white-space:pre-line;">
                                                    <?php echo $tmp_resposne; ?>
                                                </div>
                                                
                                            </div>
                                            <div class="col-xs-12 col-sm-4 answer__col-right -have-commented">
                                                <?php echo $highlights_mistakes; ?>
                                            </div>
                                        </div>
                                        <?php $review_user_panel = ob_get_clean();
                                        $user_correction = true;
                                    }
                                }

                                
                            }

                            ob_start(); ?>
                            <div class="part-score"> 
                                <span class="part-score__text">Task <?php echo $section_order; ?> Overall score:</span> 
                                <span class="part-score__val"><?php echo isset($overall['band'])? number_format(floatval($overall['band']), 1) : '2.0'; ?></span>
                            </div>
                            <div class="row">

                                <!--begin::overall feedback-->
                                <div class="col-xs-12 col-sm-12">
                                    <div class="form-group evaluation__item-card"> 
                                        <label for="textarea" class="evaluation__item-title">Overall Feedback</label>
                                        <div class="evaluation__score" style="display:none;"></div>
                                        <div class="form-control evaluation__item-comment" disabled="disabled" style="height:100%;">
                                            <div style="max-height: 100%; overflow: auto">
                                                <?php echo $overall['feedback']; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::overall feedback-->

                                <!--begin::Task Achivenment-->
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group evaluation__item-card"> 
                                        <label for="textarea" class="evaluation__item-title">Task Achievement</label>
                                        <div class="evaluation__score"><?php echo isset($ta['band'])? number_format(floatval($ta['band']), 1) : 0; ?></div>
                                        <div class="form-control evaluation__item-comment" disabled="disabled" style="height:100%;">
                                            <div style="max-height: 100%; overflow: auto">
                                                <?php echo isset($ta['feedback'])? $ta['feedback'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Task Achivenment-->

                                <!--begin::Coherence and Cohesion -->
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group evaluation__item-card"> 
                                        <label for="textarea" class="evaluation__item-title">Coherence and Cohesion</label>
                                        <div class="evaluation__score"><?php echo isset($cc['band'])? number_format(floatval($cc['band']), 1) : 0; ?></div>
                                        <div class="form-control evaluation__item-comment" disabled="disabled" style="height:100%;">
                                            <div style="max-height: 100%; overflow: auto">
                                                <?php echo isset($cc['feedback'])? $cc['feedback'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Coherence and Cohesion-->

                                <!--begin::Lexical Resource-->
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group evaluation__item-card"> 
                                        <label for="textarea" class="evaluation__item-title">Lexical Resource</label>
                                        <div class="evaluation__score"><?php echo isset($lr['band'])? number_format(floatval($lr['band']), 1) : 0; ?></div>
                                        <div class="form-control evaluation__item-comment" disabled="disabled" style="height:100%;">
                                            <div style="max-height: 100%; overflow: auto">
                                                <?php echo isset($lr['feedback'])? $lr['feedback'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Lexical Resource-->

                                <!--begin::Grammatical Range and Accuracy-->
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group evaluation__item-card"> 
                                        <label for="textarea" class="evaluation__item-title">Grammatical Range and Accuracy</label>
                                        <div class="evaluation__score"><?php echo isset($ga['band'])? number_format(floatval($ga['band']), 1) : 0; ?></div>
                                        <div class="form-control evaluation__item-comment" disabled="disabled" style="height:100%;">
                                            <div style="max-height: 100%; overflow: auto">
                                                <?php echo isset($ga['feedback'])? $ga['feedback'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Grammatical Range and Accuracy-->

                            </div>
                            <?php $review_panel_content = ob_get_clean();
                            
                        }
                        // template user base
                        if( $ai_response->format == "html") {
                            $review_panel_content = $ai_response->response;
                        }

                        // unknown response
                        if( !in_array($ai_response->format, ['json','html'])) {
                            $review_panel_content = 'BY AI: Response is invalid format, please contact to site owner';
                        }

                    } else {
                        $review_panel_content = 'BY AI: Response is invalid format, please contact to site owner';
                    }
                } else {
                    ob_start(); ?>
                    <div class="no-answer">
                        <img src="https://cdn.ieltslms.com/cdn/ielts/assets/img/icon-add-answer.svg" alt="" class="no-answer__img">
                        <h5 class="no-answer__title">Evaluation Response error</h5>
                        <p>It Looks like evaluation response is not in valid structure, contact support team.</p>
                    </div>
                    <?php $review_panel_content = ob_get_clean();
                }

                
            } else {
                $review_panel_content = 'It Looks like Response not found';
            }
        }


        



        return (object) [
            "review_user_panel"     => $review_user_panel,
            "correction"            => $user_correction,
            "evaluation"  => $review_panel_content.$extra_content
        ];
    }

    /**
     * @var display_evaluated_report_for_student_by_evaluator
     */
    public  function display_evaluated_report_for_student_by_evaluator( $params = [] ) {
        global $wpdb, $table_proqyz_quiz_review, $table_proqyz_quiz_review_meta;
        $params = (object) $params;
        $section_id     = $params->section_id;
        $section_order  = $params->section_order;
        $student_id     = $params->student_id;
        $result_id       = $params->result_id;

        # default data for review
        $review_feedback_status = 1;
        $review_feedback_content = "";
        $review_scores = [1,1,1,1];

        $get_review  = $wpdb->get_row("SELECT `ID` FROM {$table_proqyz_quiz_review} WHERE `result_id` = '$result_id' AND `user_id` = '$student_id' AND `status` = 1 AND `section_id` = '$section_id' LIMIT 1");
        # evaluated
        if( $get_review ) {
            $review_meta_id = $get_review->ID;

            # review feedback content
            $review_feedback_content_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 'review-feedback-content' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
            if( $review_feedback_content_key ) { $review_feedback_content = stripslashes($review_feedback_content_key->meta_value); }
            

            # 2.) review scores
            $review_scores_key = $wpdb->get_row("SELECT `meta_value` FROM {$table_proqyz_quiz_review_meta} WHERE `meta_key` = 'review-scores' AND `review_id` = '$review_meta_id' AND `meta_status` = 1 LIMIT 1");
            if( $review_scores_key ) { 
                $review_scores = (array) json_decode($review_scores_key->meta_value); 
            }
        }

        # get overall band score for this section
        $overall_sum = array_sum($review_scores);
        $count = count($review_scores);
        # Calculate the average
        $average = $overall_sum / $count;
        # Round the average to the nearest half band or whole band
        $overall_band = round($average * 2) / 2;

        ob_start(); ?>
            <div class="part-score"> 
                <span class="part-score__text">Task <?php echo $section_order; ?> Overall score:</span> 
                <span class="part-score__val"><?php echo number_format(floatval($overall_band), 1); ?></span>
            </div>
            <div class="row">
                <!--begin::overall feedback -->
                <div class="col-xs-12 col-sm-12 mb-2">
                    <div class="form-group evaluation__item-card"> 
                        <label for="textarea" class="evaluation__item-title">Overall Feedback</label>
                        <div class="evaluation__score" style="display:none;">

                        </div>
                        <div class="form-control evaluation__item-comment" disabled="disabled" style="height:100%;">
                            <div style="max-height: 100%; overflow: auto">
                                <?php echo $review_feedback_content; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::overall feedback -->

                <!-- begin::Task Achivenment -->
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group evaluation__item-card"> 
                        <label for="textarea" class="evaluation__item-title">Task Achievement</label>
                        <div class="evaluation__score">
                            <!--begin::band score selector-->
                            <?php echo isset($review_scores[0])? number_format(floatval($review_scores[0]), 1) : '0'; ?>
                            <!--end::band score selector-->
                        </div>
                        <div class="form-control evaluation__item-comment --review" disabled="disabled" style="height:100%;">
                            <div style="max-height: 100%; overflow: auto">
                                <?php echo isset($this->Task_1_response[$review_scores[0]])?  $this->Task_1_response[$review_scores[0]] : ''; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end::Task Achivenment -->

                <!-- begin::Coherence and Cohesion -->
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group evaluation__item-card"> 
                        <label for="textarea" class="evaluation__item-title">Coherence and Cohesion</label>
                        <div class="evaluation__score">
                            <!--begin::band score selector--> 
                            <?php echo isset($review_scores[1])? number_format(floatval($review_scores[1]), 1) : '0'; ?>
                            <!--end::band score selector-->
                        </div>
                        <div class="form-control evaluation__item-comment" disabled="disabled" style="height:100%;">
                            <div style="max-height: 100%; overflow: auto">
                                <?php echo isset($this->coherence_lines[$review_scores[1]])?  $this->coherence_lines[$review_scores[1]] : ''; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end::Coherence and Cohesion -->

                <!-- begin::Lexical Resource -->
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group evaluation__item-card"> 
                        <label for="textarea" class="evaluation__item-title">Lexical Resource</label>
                        <div class="evaluation__score">
                            <!--begin::band score selector--> 
                            <?php echo isset($review_scores[2])? number_format(floatval($review_scores[2]), 1) : '0'; ?>
                            <!--end::band score selector-->
                        </div>
                        <div class="form-control evaluation__item-comment" disabled="disabled" style="height:100%;">
                            <div style="max-height: 100%; overflow: auto">
                                <?php echo isset($this->Lexical_lines[$review_scores[2]])?  $this->Lexical_lines[$review_scores[2]] : ''; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end::Lexical Resource -->

                <!-- begin::Grammatical Range and Accuracy -->
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group evaluation__item-card"> 
                        <label for="textarea" class="evaluation__item-title">Grammatical Range and Accuracy</label>
                        <div class="evaluation__score">
                            <!--begin::band score selector-->
                            <?php echo isset($review_scores[3])? number_format(floatval($review_scores[3]), 1) : '0'; ?>
                            <!--end::band score selector-->
                        </div>
                        <div class="form-control evaluation__item-comment" disabled="disabled" style="height:100%;">
                            <div style="max-height: 100%; overflow: auto">
                                <?php echo isset($this->grammatical_lines[$review_scores[3]])?  $this->grammatical_lines[$review_scores[3]] : ''; ?>
                            </div>
                        </div>
                    </div>
                </div>                        
                <!-- end::Grammatical Range and Accuracy -->            
            </div>
        <?php return ob_get_clean();
    }

    /**
     * @category sample-review overall report
     */
    public function sample_overall_review_report() {
        ?>
        <!-- begin:: open ai -->
        <div class="head-card -sample">
                                                <div class="head-card__contents">
                                                    <div class="head-card__col -right">
                                                        <div class="score-panel">
                                                            <h2 class="score-panel__title">
                                                                FINAL SCORE FROM <strong>IELTS-GPT Evaluation</strong>
                                                            </h2>
                                                            <div class="score-panel__scores">
                                                                <div class="score-panel__score-item -big">
                                                                    <div class="score-panel__score-item-title">Overall</div>
                                                                    <div class="score-panel__score-item-val">6.0</div>
                                                                </div>
                                                                <div class="score-panel__score-item -small">
                                                                    <div class="score-panel__score-item-title">Task 1</div>
                                                                    <div class="score-panel__score-item-val">6.0</div>
                                                                </div>
                                                                <div class="score-panel__score-item -small">
                                                                    <div class="score-panel__score-item-title">Task 2</div>
                                                                    <div class="score-panel__score-item-val">6.5</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="final-score">
                                                <h3 class="final-score__title">
                                                    Overall Score <span class="final-score__result">6.0</span>
                                                </h3>
                                                <div class="final-score__comment-box">
                                                    <h5 
                                                        class="final-score__comment-panel clearfix collapsed"
                                                        aria-expanded="false" 
                                                        data-toggle="collapse" 
                                                        data-parent="#final-score-panel" 
                                                        href="#final-score-panel"
                                                    >
                                                        General Feedback
                                                    </h5>
                                                    
                                                    <div id="final-score-panel" class="final-score__comment collapse" style="display:none;height: 0px;" aria-expanded="false">
                                                        <div class="final-score__comment-contents -disabled"> 
                                                            Your essays show a good understanding of the topics. However, attention to grammatical accuracy, vocabulary usage, and the flow of ideas could be improved to enhance overall coherence and clarity of your arguments.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end:: open ai -->
        <?php 
    }

    /**
     * @category plans
     * since 2.0.3.1
     * check evaluation plans - move from constructor
     */
    public function check_evaluation_plans() {
        global
            $ST_OPTION_st_proqyz_IELTS_LMS_enable_evaluation,
            $ST_OPTION_st_proqyz_IELTS_LMS_writing_task1_evaluation_enable,
            $ST_OPTION_st_proqyz_IELTS_LMS_writing_task2_evaluation_enable,
            $ST_OPTION_st_proqyz_IELTS_LMS_writing_task1and2_evaluation_enable,
            $ST_OPTION_st_proqyz_IELTS_LMS_writing_task1_evaluation_plan_id,
            $ST_OPTION_st_proqyz_IELTS_LMS_writing_task2_evaluation_plan_id,
            $ST_OPTION_st_proqyz_IELTS_LMS_writing_task1and2_evaluation_plan_id,
            $ST_OPTION_st_proqyz_IELTS_LMS_writing_evaluation_instructions;
        global
            $ST_OPTION_st_proqyz_IELTS_LMS_enable_ai_evaluation,
            $ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task1_evaluation_enable,
            $ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task2_evaluation_enable,
            $ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task1and2_evaluation_enable,
            $ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task1_evaluation_plan_id,
            $ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task2_evaluation_plan_id,
            $ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task1and2_evaluation_plan_id,
            $ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_evaluation_instructions;



        if( $this->result || $this->review ) {
            # get plans

            # check if writing evaluation is enabled
            $enable_evaluation      = get_option($ST_OPTION_st_proqyz_IELTS_LMS_enable_evaluation,false);
            $enable_ai_evaluation   = get_option($ST_OPTION_st_proqyz_IELTS_LMS_enable_ai_evaluation,false);
            if($this->PROQYZ_EVALUATION == 'true'){
                if( $enable_evaluation || $enable_ai_evaluation ){ 

                    # if payment plugin is not enabled which means tasks are also marked as paid
                    if( !class_exists('woocommerce') ) {
                        return;
                    }

                    if( $enable_evaluation ) $this->IS_NORMAL_EVALUATION_ENABLE = true;
                    if( $enable_ai_evaluation ) $this->IS_AI_EVALUATION_ENABLE = true;




                    $db_student_id              = $this->_result->user_id;
                    $db_result_id               = $this->result_id;

                    $currency_code              = get_woocommerce_currency();
                    $sign                       = get_woocommerce_currency_symbol( $currency_code );

                    
                    # without ai
                    $task1_enable                   = (int) get_option($ST_OPTION_st_proqyz_IELTS_LMS_writing_task1_evaluation_enable,0);

                    $task2_enable                   = (int) get_option($ST_OPTION_st_proqyz_IELTS_LMS_writing_task2_evaluation_enable,0);
                    // $task1and2_enable               = (int) get_option($ST_OPTION_st_proqyz_IELTS_LMS_writing_task1and2_evaluation_enable,0);

                    # with ai
                    $ai_task1_enable                = (int) get_option($ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task1_evaluation_enable,0);
                    $ai_task2_enable                = (int) get_option($ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task2_evaluation_enable,0);
                    // $ai_task1and2_enable            = (int) get_option($ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task1and2_evaluation_enable,0);

                

                    # get plans and pricing
                    if( $this->HAS_TASKS_1 ){

                        
                        # if task evaluation root is enable
                        if( $this->IS_NORMAL_EVALUATION_ENABLE && $task1_enable == 1 ){
                            # GET PRODUCT TASK 1
                            $WRITING_task_1_product_id = get_option($ST_OPTION_st_proqyz_IELTS_LMS_writing_task1_evaluation_plan_id,false);
                            if ( $WRITING_task_1_product_id ) {
                                $WRITING_task_1_product = wc_get_product( $WRITING_task_1_product_id );
                                if( $WRITING_task_1_product ){
                                    if( $WRITING_task_1_product->get_price() > 0 ) {
                                        $this->HAS_TASK1_PLAN = true; 
                                        $this->TASK1_PLAN = (object) [
                                            "title"     => esc_html($WRITING_task_1_product->get_title()),
                                            "price"     => $sign.esc_html($WRITING_task_1_product->get_price())
                                        ];
                                        $this->balance += $WRITING_task_1_product->get_price();
                                        
                                        
                                    }
                                }
                            } 
                        }  
                        
                        if( $this->IS_AI_EVALUATION_ENABLE && $ai_task1_enable == 1 ) {
                            # GET PRODUCT TASK 1
                            $ai_WRITING_task_1_product_id = get_option($ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task1_evaluation_plan_id,false);
                            if ( $ai_WRITING_task_1_product_id ) {
                                $ai_WRITING_task_1_product = wc_get_product( $ai_WRITING_task_1_product_id );
                                if( $ai_WRITING_task_1_product ){
                                    if( $ai_WRITING_task_1_product->get_price() > 0 ) {
                                        $this->HAS_AI_TASK1_PLAN = true;                                    
                                        $this->AI_TASK1_PLAN = (object) [
                                            "title"     => esc_html($ai_WRITING_task_1_product->get_title()),
                                            "price"     => $sign.esc_html($ai_WRITING_task_1_product->get_price())
                                        ];
                                        $this->balance += $ai_WRITING_task_1_product->get_price();
                                    }
                                }
                            } 
                        }

                    } 
                        
                    if( $this->HAS_TASKS_2 ){
                        if( $this->IS_NORMAL_EVALUATION_ENABLE && $task2_enable == 1 ){
                            # GET PRODUCT TASK 2
                            $WRITING_task_2_product_id = get_option($ST_OPTION_st_proqyz_IELTS_LMS_writing_task2_evaluation_plan_id,false);
                            if ( $WRITING_task_2_product_id ) {
                                $WRITING_task_2_product = wc_get_product( $WRITING_task_2_product_id );
                                if( $WRITING_task_2_product ){
                                    if( $WRITING_task_2_product->get_price() > 0 ) {
                                        $this->HAS_TASK2_PLAN = true;                                   
                                        $this->TASK2_PLAN = (object) [
                                            "title"     => esc_html($WRITING_task_2_product->get_title()),
                                            "price"     => $sign.esc_html($WRITING_task_2_product->get_price())
                                        ];
                                        $this->balance += $WRITING_task_2_product->get_price();
                                    }
                                }
                            }
                        }

                        if( $this->IS_AI_EVALUATION_ENABLE && $ai_task2_enable == 1 ){
                            # GET PRODUCT TASK 2
                            $ai_WRITING_task_2_product_id = get_option($ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task2_evaluation_plan_id,false);
                            if ( $ai_WRITING_task_2_product_id ) {
                                $ai_WRITING_task_2_product = wc_get_product( $ai_WRITING_task_2_product_id );
                                if( $ai_WRITING_task_2_product ){
                                    if( $ai_WRITING_task_2_product->get_price() > 0 ) {
                                        $this->HAS_AI_TASK2_PLAN = true;                                   
                                        $this->AI_TASK2_PLAN = (object) [
                                            "title"     => esc_html($ai_WRITING_task_2_product->get_title()),
                                            "price"     => $sign.esc_html($ai_WRITING_task_2_product->get_price())
                                        ];
                                        $this->balance += $ai_WRITING_task_2_product->get_price();
                                    }
                                }
                            }
                        }
                    }

                    /*
                    if( $this->HAS_TASKS_1 && $this->HAS_TASKS_2 ) {
                        
                        if($this->is_evaluation_enable && $task1and2_enable == 1){
                            $WRITING_task_1_and_2_product_id = (int) get_option($ST_OPTION_st_proqyz_IELTS_LMS_writing_task1and2_evaluation_plan_id);
                            if( $WRITING_task_1_and_2_product_id ){
                                
                                $WRITING_task_1_and_2_product = wc_get_product( $WRITING_task_1_and_2_product_id );
                                if( $WRITING_task_1_and_2_product ){
                                    
                                    if( $WRITING_task_1_and_2_product->get_price() > 0 ) {
                                        $this->task1and2_plan = true;
                                        $this->task1and2_plan_details = (object) [
                                            "title"     => esc_html($WRITING_task_1_and_2_product->get_title()),
                                            "price"     => $sign.esc_html($WRITING_task_1_and_2_product->get_price())
                                        ];
                                    }
                                }
                            
                            }
                        }

                        if($this->is_ai_evaluation_enable && $ai_task1and2_enable == 1){
                            $ai_WRITING_task_1_and_2_product_id = (int) get_option($ST_OPTION_st_proqyz_IELTS_LMS_ai_writing_task1and2_evaluation_plan_id);
                            if( $ai_WRITING_task_1_and_2_product_id ){
                                $ai_WRITING_task_1_and_2_product = wc_get_product( $ai_WRITING_task_1_and_2_product_id );
                                if( $ai_WRITING_task_1_and_2_product ){
                                    if( $ai_WRITING_task_1_and_2_product->get_price() > 0 ) {
                                        $this->ai_task1and2_plan = true;
                                        $this->ai_task1and2_plan_details = (object) [
                                            "title"     => esc_html($ai_WRITING_task_1_and_2_product->get_title()),
                                            "price"     => $sign.esc_html($ai_WRITING_task_1_and_2_product->get_price())
                                        ];
                                    }
                                }
                            }
                        }
                    }
                    */



                    // if($this->HAS_TASKS_1 && $this->HAS_TASKS_2 && ($this->task1and2_plan || $this->ai_task1and2_plan)){
                    //     $this->task1_paid = false;
                    //     $this->task2_paid = false;
                    // }


                    /*
                    if ( in_array("task-1", $enrolled_for ) || in_array("ai-task-1", $enrolled_for ) ){ 
                        $this->task1_paid = true; 

                        if(in_array('task-1', $enrolled_for)) {
                            $this->task1_paid_as = 'enrolled';
                        }

                        if(in_array('ai-task-1', $enrolled_for)) {
                            $this->task1_paid_as = 'ai-enrolled';
                        }
                    }
                    if ( in_array( "task-2", $enrolled_for ) || in_array("ai-task-2", $enrolled_for ) ) { 
                        $this->task2_paid = true; 

                        if(in_array('task-2', $enrolled_for)) {
                            $this->task2_paid_as = 'enrolled';
                        }

                        if(in_array('ai-task-2', $enrolled_for)) {
                            $this->task2_paid_as = 'ai-enrolled';
                        }
                    } 
                    */

                    if( $this->balance <= 0 ) {
                        $this->IS_ALL_TASKS_PAID = true;
                        $this->evaluation_reason = "wp-evaluation-no-cost";
                    }

                } else {
                    # if evaluation is not enabled which means mark as tasks paid
                    $this->IS_ALL_TASKS_PAID = true;
                    $this->evaluation_reason = "wp-evaluation-disabled";
                }
            } else {
                $this->IS_ALL_TASKS_PAID = true;
                $this->evaluation_reason = 'proqyz-evaluation-disabled';
                
            }

            

        }
    }

    /**
     * get plans
     */
    public function get_plans_layout() {
    
        $plan_selector = "";
        $plans_modals   = "";

        if( $this->IS_NORMAL_EVALUATION_ENABLE || $this->IS_AI_EVALUATION_ENABLE ){
            $plans          = $this->get_evalution_plans();
            if( $plans ){
                $plan_selector = $plans->plans_selector;
                $plans_modals   = $plans->plans_modal;
            }
        }

    
        ob_start(); 
        echo $plan_selector;
        echo $plans_modals; 
        return ob_get_clean();
    }


    /**
     * get plans sidebar
     */
    public function get_evalution_plans() {
        global 
		$ST_OPTION_st_proqyz_IELTS_LMS_enable_evaluation,
		$ST_OPTION_st_proqyz_IELTS_LMS_writing_task1_evaluation_enable,
		$ST_OPTION_st_proqyz_IELTS_LMS_writing_task2_evaluation_enable,
		$ST_OPTION_st_proqyz_IELTS_LMS_writing_task1and2_evaluation_enable,
		$ST_OPTION_st_proqyz_IELTS_LMS_writing_task1_evaluation_plan_id,
		$ST_OPTION_st_proqyz_IELTS_LMS_writing_task2_evaluation_plan_id,
		$ST_OPTION_st_proqyz_IELTS_LMS_writing_task1and2_evaluation_plan_id,
		$ST_OPTION_st_proqyz_IELTS_LMS_writing_evaluation_instructions;

        $writing_instructions   = get_option($ST_OPTION_st_proqyz_IELTS_LMS_writing_evaluation_instructions,'');
    
        $modal_task_pay         = "";
        $plan_selector          = "";
        


        ob_start(); ?>
            <div class="service-box -correction">
                <div class="service-box__content">
                    
                    <?php echo stripslashes($writing_instructions); ?>

                    <div class="test-submit-page -writing">
                        <div class="card-successful__col">
                            <div class="card-successful__box service-price-box">
                                <div class="service-price-box__contents">
                                    <div class="service-price-box__price">
                                        <div class="service-price-box__col-name">
                                            <div class="service-price-box__service-name">Writing evaluation service</div>
                                            <p class="service-price-box__service-task"></p>
                                        </div>
                                        <div class="service-price-box__col-price">
                                            <div class="service-price-box__item-price"></div>
                                            <div class="service-price-box__item-price -en"></div>
                                        </div>
                                    </div>
                                    <a class="service-price-box__btn-change" data-toggle="modal" data-target="#modal-task-pay">Change option</a>

                                    <div class="service-price-box__btn-wrap">
                                        <form id="st-proqyz-ieltslms-evaluation-cart-form">
                                            <input type="hidden" name="selected-task-id" value="" id="selected-task-id" /> 
                                            <input type="hidden" name="selected-task" value="" id="selected-task" /> 
                                            <input type="hidden" name="selected-result-id" value="<?php echo $this->result_id; ?>" id="selected-result-id" />
                                            <button style="max-width:100%;" class="service-price-box__btn iot-bt" type="submit">Buy Now</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        <?php $plan_selector = ob_get_clean();


        ob_start(); ?>
            <div class="modal fade modal-task-pay" id="modal-task-pay" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="display: none;">
                <div class="modal-dialog">
                    <form action="#" method="post" id="change-phone-form">
                        <div class="modal-content">
                            <i class="ion-android-close close-modal" data-dismiss="modal" aria-label="Close"></i>
                            <div class="modal-header">
                                <ul class="evaluation__tabs-ul">
                                    <?php if( $this->IS_NORMAL_EVALUATION_ENABLE ){ ?>
                                        <li class="evaluation__tabs-li" data-target="#evaluator-plans" data-toggle="evaluation-plans">
                                            <span class="evaluation__tabs-span">
                                                By Instructor
                                            </span>
                                        </li>
                                    <?php } ?>
                                    <?php if( $this->IS_AI_EVALUATION_ENABLE ){ ?> 
                                        <li class="evaluation__tabs-li" data-target="#ai-evaluator-plans" data-toggle="evaluation-plans">
                                            <span class="evaluation__tabs-span">
                                                By AI
                                            </span>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <h4 class="modal-task-pay__title">Select your evaluation option</h4>
                                <div class="modal-task-pay__note">Already paid options will not display here !</div>
                            </div>
                            <?php if( $this->IS_NORMAL_EVALUATION_ENABLE ){ ?>
                                <div id="evaluator-plans" class="evaluation__tabs-content modal-body --selected-plan" style="display:none;">

                                    <?php 
                                    /*
                                    if( $this->task1and2_plan ) {  ?>
                                        <div class="modal-task-pay__item --recommended <?php echo ($this->task1_paid || $this->task2_paid || $this->is_tasks_reviewd)? 'disabled' : 'avilable'; ?>" data-taskname="<?php echo $this->task1and2_plan_details->title; ?>" data-price="<?php echo $this->task1and2_plan_details->price; ?>" data-type="tasks" data-slug="task-1-and-2"> 
                                            <span class="modal-task-pay__rec-label">Recommended</span>
                                            <span class="modal-task-pay__item-name"><?php echo $this->task1and2_plan_details->title; ?></span> 
                                            <span class="modal-task-pay__item-price"><?php echo $this->task1and2_plan_details->price; ?></span> 
                                        </div> 
                                    <?php } 
                                    */
                                    ?>

                                    <?php if( $this->HAS_TASK1_PLAN && count($this->TASK1_SELECTOR_LAYOUT) > 0 ) {  ?>
                                        <?php foreach( $this->TASK1_SELECTOR_LAYOUT as $selector ) { ?>
                                            <div data-task-label="By Instructor" class="modal-task-pay__item " data-task-id="<?php echo $selector->value; ?>" data-taskname="<?php echo $selector->label; ?>" data-price="<?php echo $this->TASK1_PLAN->price; ?>" data-type="tasks" data-slug="task-1"> 
                                                <span class="modal-task-pay__item-name"><?php echo $selector->label; ?></span> 
                                                <span class="modal-task-pay__item-price"><?php echo $this->TASK1_PLAN->price; ?></span> 
                                            </div> 
                                        <?php } ?> 
                                    <?php } ?>

                                    <?php if( $this->HAS_TASK2_PLAN && count($this->TASK2_SELECTOR_LAYOUT) > 0) {  ?>
                                        <?php foreach( $this->TASK2_SELECTOR_LAYOUT as $selector ) { ?>
                                            <div data-task-label="By Instructor" class="modal-task-pay__item " data-task-id="<?php echo $selector->value; ?>" data-taskname="<?php echo $selector->label; ?>" data-price="<?php echo $this->TASK2_PLAN->price; ?>" data-type="tasks" data-slug="task-2"> 
                                                <span class="modal-task-pay__item-name"><?php echo $selector->label; ?></span> 
                                                <span class="modal-task-pay__item-price"><?php echo $this->TASK2_PLAN->price; ?></span> 
                                            </div> 
                                        <?php } ?> 
                                    <?php } ?>

                                    

                                </div>
                            <?php } ?>
                            <?php if( $this->IS_AI_EVALUATION_ENABLE ){ ?> 
                                <div id="ai-evaluator-plans" class="evaluation__tabs-content modal-body" style="display:none;">

                                    <?php /*
                                    if( $this->ai_task1and2_plan ) {  ?>
                                        <div class="modal-task-pay__item --recommended <?php echo ($this->task1_paid || $this->task2_paid || $this->is_tasks_reviewd)? 'disabled' : 'avilable'; ?>" data-taskname="<?php echo $this->ai_task1and2_plan_details->title; ?>" data-price="<?php echo $this->ai_task1and2_plan_details->price; ?>" data-type="tasks" data-slug="ai-task-1-and-2"> 
                                            <span class="modal-task-pay__rec-label">Recommended</span>
                                            <span class="modal-task-pay__item-name"><?php echo $this->ai_task1and2_plan_details->title; ?></span> 
                                            <span class="modal-task-pay__item-price"><?php echo $this->ai_task1and2_plan_details->price; ?></span> 
                                        </div> 
                                    <?php } 
                                    */
                                    ?>

                                    <?php if( $this->HAS_AI_TASK1_PLAN && count($this->TASK1_SELECTOR_LAYOUT) > 0 ) {  ?>
                                        <?php foreach( $this->TASK1_SELECTOR_LAYOUT as $selector ) { ?>
                                            <div data-task-label="By AI" class="modal-task-pay__item " data-task-id="<?php echo $selector->value; ?>" data-taskname="<?php echo $selector->label; ?>" data-price="<?php echo $this->AI_TASK1_PLAN->price; ?>" data-type="tasks" data-slug="ai-task-1"> 
                                                <span class="modal-task-pay__item-name"><?php echo $selector->label; ?></span> 
                                                <span class="modal-task-pay__item-price"><?php echo $this->AI_TASK1_PLAN->price; ?></span> 
                                            </div> 
                                        <?php } ?>
                                    <?php } ?>

                                    <?php if( $this->HAS_AI_TASK2_PLAN && count($this->TASK2_SELECTOR_LAYOUT) > 0 ) {  ?>
                                        <?php foreach( $this->TASK2_SELECTOR_LAYOUT as $selector ) { ?>
                                            <div data-task-label="By AI" class="modal-task-pay__item " data-task-id="<?php echo $selector->value; ?>" data-taskname="<?php echo $selector->label; ?>" data-price="<?php echo $this->AI_TASK2_PLAN->price; ?>" data-type="tasks" data-slug="ai-task-2"> 
                                                <span class="modal-task-pay__item-name"><?php echo $selector->label; ?></span> 
                                                <span class="modal-task-pay__item-price"><?php echo $this->AI_TASK2_PLAN->price; ?></span> 
                                            </div> 
                                        <?php } ?> 
                                    <?php } ?>

                                    

                                </div>
                            <?php } ?>
                            <div class="modal-footer"> 
                                <button type="submit" class="iot-bt -orange" data-dismiss="modal">Done</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        <?php $modal_task_pay = ob_get_clean();

        

        return (object) [
            "plans_selector" => $plan_selector,
            "plans_modal"    => $modal_task_pay   
        ];
    }


    /**
     * below is all common user functions
     */
    public function login_notice() {
        if(!is_user_logged_in()) {

            global 
			$ST_OPTION_st_proqyz__auth_redirect_enable_key,
			$ST_OPTION_st_proqyz__auth_signup_url_key,
			$ST_OPTION_st_proqyz__auth_signin_url_key;


            $redirect_url           = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            
            $enable_auth_redirect   = get_option($ST_OPTION_st_proqyz__auth_redirect_enable_key,0);
            $auth_signin_url        = get_option($ST_OPTION_st_proqyz__auth_signin_url_key,"/wp-login.php");
            $auth_signup_url        = get_option($ST_OPTION_st_proqyz__auth_signup_url_key,"/wp-login.php");
            $url                    = $auth_signin_url;
            $url                    .= $enable_auth_redirect == 1? '?redirect_to='.$redirect_url : '';
            

            ob_start(); ?>
            <div class="test-notice modal-test-notice"> 
                Please login to take the test. Click 
                <a class="test-notice__link" href="<?php echo $url; ?>">Here</a> 
                or the button below to login now. 
                <a href="<?php echo $url; ?>" class="test-notice__btn iot-bt">
                    Log in
                </a>
            </div>
            <?php return ob_get_clean();
        }
    }

    /**
     * changes @1.2.1.2
     */
    public function modals(){
        if($this->result){
            ob_start(); ?>
                
            <?php return ob_get_clean();
        } else {
            $assets_url = site_url() . "/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist";
            ob_start(); ?>

                <!--div class="modal fade modal-submit-test" id="modal-submit-test" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="display:none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <i class="ioticon-x modal-submit-test__close-modal" data-dismiss="modal" aria-label="Close"></i>
                            <div class="modal-body">
                                <div class="modal-submit-test__icon"></div>
                                <h4 class="modal-submit-test__title">Are you sure you want to submit?</h4>
                                <div class="modal-submit-test__footer">
                                    <button type="button" class="modal-submit-test__btn iot-grbt -white" data-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button id="modal-submit-test__btn" data-id="<?php echo $this->_id; ?>" data-category="<?php echo $this->category; ?>" type="button" class="modal-submit-test__btn iot-grbt -main-color -btn-submit-test">
                                        Submit and Review Answers
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div--> 

                <div class="modal fade modal-submit-essay" id="modal-submit-essay" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="display: none;">
                    <div class="modal-dialog modal-auto modal-submit-essay">
                        <div class="modal-content-wp"> 
                            <i class="close-modal -blue" data-dismiss="modal" aria-label="Close"></i>
                            <div class="modal-submit-essay__contents clearfix">
                                <div class="modal-submit-essay__icon-wrap"> 
                                    <img src="<?php echo $assets_url; ?>/img/ielts/icons/submit-essay.svg" alt="" class="modal-submit-essay__icon" />
                                </div>
                                <h2 class="modal-submit-essay__title">You’re ready to submit your essay?</h2>
                                <p class="modal-submit-essay__caption">
                                    You won’t be able to edit your essay after submitting so if you’re ready, click Submit for evaluation.
                                </p>
                            </div>
                            <div class="modal-submit-essay_btn-wrap clearfix"> 
                                <button type="button" class="iot-bt -grey-blue proqyz_btn" data-dismiss="modal">
                                    Let me check again
                                </button> 
                                <button id="modal-submit-test__btn" type="button" class="iot-bt -btn-submit-test proqyz_btn" data-id="<?php echo $this->_id; ?>" data-category="<?php echo $this->category; ?>">
                                    Yes, I’m ready!
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade modal-submit-test" id="modal-do-not-work-lr" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="display:none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <i class="ioticon-x modal-submit-test__close-modal" data-dismiss="modal" aria-label="Close"></i>
                            <div class="modal-body">
                                <div class="modal-submit-test__icon"></div>
                                <h4 class="modal-submit-test__title">Please select an answer!</h4>
                            </div>
                        </div>
                    </div>
                </div>


                <?php if(!$this->is_fullmock_test){ ?> <div class="modal fade modal-time-up" id="modal-finish" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-keyboard="false" data-backdrop="static" style="display:none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="modal-time-up__icon"></div>
                                <h4 class="modal-time-up__title">This test has been done</h4>
                                <div class="modal-time-up__footer">
                                    <button type="button" class="modal-time-up__btn iot-grbt -main-color -btn-redirect-result">
                                        You are being redirected to result page
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <div class="modal fade modal-time-up" id="modal-time-up-no-taketest" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-keyboard="false" data-backdrop="static" style="display:none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="modal-time-up__icon"></div>
                                <h4 class="modal-time-up__title">Time is up</h4>
                                <?php if(!$this->is_fullmock_test){ ?>
                                <div class="modal-time-up__desc">
                                    However, we realize that you have not completed the test yet. <br />
                                    Please click the "Retake" button below to take the test again before submitting.
                                </div>
                                <div class="modal-time-up__footer">
                                    <button type="button" class="modal-time-up__btn iot-grbt -main-color -btn-retake">
                                        Retake<span class="ioticon-send-v2"></span>
                                    </button>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade modal-time-up" id="modal-time-up" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-keyboard="false" data-backdrop="static" style="display:none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="modal-time-up__icon"></div>
                                <h4 class="modal-time-up__title">Time is up</h4>
                                <?php if(!$this->is_fullmock_test){ ?>
                                <div class="modal-time-up__footer">
                                    <button type="button" class="modal-time-up__btn iot-grbt -main-color -btn-submit-test">
                                        Submit and Review Answers<span class="ioticon-send-v2"></span>
                                    </button>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade modal-time-up" id="modal-handle-errors" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-keyboard="false" data-backdrop="static" style="display:none;background:#ef7474d9;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body bg-danger">
                                <div class="modal-time-up__icon"></div>
                                <h4 class="modal-time-up__title">Validation error</h4>
                                <div class="modal-time-up__desc"></div>
                                <div class="modal-time-up__footer">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade modal-view-solution" id="modal-resume" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="display: none;" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content"> 
                            
                            <div class="modal-body">
                                <div class="modal-view-solution__icon modal-icon-wrap">
                                    <img src="<?php echo $assets_url; ?>/img/ielts/icons/retake-test.svg" alt="" class="modal-icon_center" />
                            
                                </div>
                                <h4 class="modal-view-solution__title">Are you sure you want to Resume your test?</h4>
                                <p class="modal-view-solution__caption"> 
                                    *This action will <strong>NOT SUBMIT</strong> your test and your answers will be <strong>LOST</strong>
                                </p>
                                <div class="modal-view-solution__footer"> 
                                    <button id="btn-continue" class="modal-view-solution__btn iot-grbt -main-color">
                                        Continue 
                                    </button> 
                                    <button id="btn-restart" type="button" class="modal-view-solution__btn iot-grbt -white">
                                        Restart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade modal-iot" id="modal-not-taketest" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="display: none;">
                    <div class="modal-dialog modal-auto">
                        <div class="">
                            <i class="ion-android-close close-modal" data-dismiss="modal" aria-label="Close"> </i>
                            <div class="modal-icon-wrap"> 
                                <img src="<?php echo $assets_url; ?>/img/ielts/icons/retake-test.svg" alt="" class="modal-icon_center" />
                            </div>
                            <h2 class="modal-caption">You haven't taken the test yet</h2>
                            <p class="modal-des">Please do the test before submitting</p>
                            <div class="modal-body"> <a class="iot-bt" data-dismiss="modal" href="">Continue</a></div>
                        </div>
                    </div>
                </div>


            <?php return ob_get_clean();
        }
    }

    /**
     * changes @1.2.1.2
     */
    public function header(){
        if( $this->solution ){ 
            ob_start(); ?>
            <head>
                <meta charset="<?php bloginfo('charset'); ?>" />    
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link rel="stylesheet" href="<?php echo site_url() . '/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/css/polyfixes.css'; ?>" />
                <link rel="stylesheet" href="<?php echo plugins_url('/', __FILE__) . 'dist/css/iot-reading-result.css'; ?>" />
                <?php echo $this->get_writing_styles(); ?>
            </head>
            <?php return ob_get_clean();
        } else if( $this->result ) {
            ob_start(); ?>
            <head>
                <?php if(isset($this->quiz->title)) { ?>
                <title><?php echo $this->quiz->title; ?> | Result</title>    
                <?php } ?>

                <meta charset="<?php bloginfo('charset'); ?>" />    
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link rel="stylesheet" href="<?php echo site_url() . '/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/css/polyfixes.css'; ?>" />
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.1/css/all.min.css" integrity="sha512-SUwyLkI1Wgm4aEZkDkwwigXaOI2HFLy1/YW73asun4sfvlkB9Ecl99+PHfCnfWD0FJjIuFTvWMM7BZPXCckpAA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
                <?php echo $this->dynamic_css_imports(); ?>
                <?php echo $this->dynamic_js_imports_header(); ?>
                <link rel="stylesheet" href="<?php echo site_url();?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/css/proqyz-quiz.css" />
                <link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/css/iot-writing.css" />
                <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet" />
                <?php echo $this->get_writing_styles(); ?>
                <style>
                    @media (max-width: 1025px){
                        .container {
                            width: 100% !important;
                        }
                    }

                    .badge-warning.-writing {
                        background: #FAAA5A !important;
                    }

                    .ajax-cart-loading .ajax-cart-loading-overlay {
                        display: flex !important;
                        position: fixed;
                        z-index: 9999;
                        background: #00000080;
                        top: 0;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        flex-direction: column;
                        flex-wrap: nowrap;
                        align-content: center;
                        justify-content: center;
                        align-items: center;
                    }

                    .ajax-cart-loading {
                        overflow: hidden;
                    }

                    .spanner {
                        position: relative;
                        left: 0;
                        display: block;
                        text-align: center;
                        color: #FFF;
                    }

                    .loader,
                    .loader:before,
                    .loader:after {
                        border-radius: 50%;
                        width: 2.5em;
                        height: 2.5em;
                        -webkit-animation-fill-mode: both;
                        animation-fill-mode: both;
                        -webkit-animation: load7 1.8s infinite ease-in-out;
                        animation: load7 1.8s infinite ease-in-out;
                    }

                    .loader {
                        color: #ffffff;
                        font-size: 5px;
                        position: relative;
                        text-indent: -9999em;
                        -webkit-transform: translateZ(0);
                        -ms-transform: translateZ(0);
                        transform: translateZ(0);
                        -webkit-animation-delay: -0.16s;
                        animation-delay: -0.16s;
                    }

                    .loader:before,
                    .loader:after {
                        content: '';
                        position: absolute;
                        top: 0;
                    }

                    .loader:before {
                        left: -3.5em;
                        -webkit-animation-delay: -0.32s;
                        animation-delay: -0.32s;
                    }
                    .loader:after {
                        left: 3.5em;
                    }

                    @-webkit-keyframes load7 {
                        0%,
                        80%,
                        100% {
                            box-shadow: 0 2.5em 0 -1.3em;
                        }
                        40% {
                            box-shadow: 0 2.5em 0 0;
                        }
                    }

                    @keyframes load7 {
                        0%,
                        80%,
                        100% {
                            box-shadow: 0 2.5em 0 -1.3em;
                        }
                        40% {
                            box-shadow: 0 2.5em 0 0;
                        }
                    }


                    .ajax-spinner-inner {
                        display: flex;
                        flex-direction: column;
                        flex-wrap: nowrap;
                        align-content: center;
                        justify-content: center;
                        align-items: center;
                    }

                    button.cancel-ajax-cart {
                        background: white;
                        border: 0;
                        width: 100px;
                        margin-top: 10px;
                    }


                    .modal-task-pay__item.disabled {
                        cursor: no-drop;
                        opacity: 0.6;
                    }

                    button.service-price-box__btn[disabled] {
                        opacity: 0.8;
                        cursor: no-drop;
                    }

                    .badge.badge-success {
                        background: #8BC34A !important;
                    }

                    .col-left {
                        margin: 0 auto !important;
                    }

                    .white-space__pre-wrap {
                        white-space: pre-wrap;
                    }
                    
                    .-writing--btn::after {
                        content: '' !important;
                    }

                </style>
                <style class="writing-evalaution-plans-style">
                    ul.evaluation__tabs-ul{
                        list-style: none;
                        margin: 0 auto 10px;
                        display: flex;
                        width: 100%;
                        align-items: center;
                        justify-content: center;
                        flex-wrap: nowrap;
                    }
                    .evaluation__tabs-span {
                        display: flex;
                        align-items: center;
                        text-align: center;
                        border: 1px solid;
                        font-size: 12px;
                        padding: 2px 10px;
                        border-radius: 30px;
                        width: 100px;
                        height: 30px;
                        justify-content: center;
                        cursor: pointer;
                        background: #efeeee;
                    }
                    li.evaluation__tabs-li.evaluation__plan--selected span {
                        border: 1px solid #faaa5a;
                        background: #feeede;
                        color: #ea8622;
                    }
                    .evaluation__tabs-content.--selected-plan {
                        display: block !important;
                    }
                    li.evaluation__tabs-li {
                        margin: 0 5px;
                    }

                    .writing-essay-page .panel-title > a.no-arrow:after {
                        content: '';
                        display: none;
                        
                    }

                    .writing-essay-page .panel-title > a.no-arrow .badge {
                        right: 15px !important;
                    }

                    .writing-essay-page .col-right {
                        background-color: #f5f5f5;
                        padding: 24px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 25px;
                        align-self: flex-start;
                        border-radius: 36px;
                        margin-top: 34px;
                        justify-content: center;
                    }

                    @media (min-width: 768px) and (max-width:991px) {
                        .writing-essay-page .col-right {
                            width: 100%;
                            margin: 0 auto;
                            flex-direction: row;
                            flex-wrap: wrap;
                            gap: 10px;
                        }
                    }

                    @media (max-width: 767px) {
                        .writing-essay-page .col-right {
                            padding-top:0;
                            background-color: #fff;
                            max-width: initial;
                        }
                    }

                    .tasks__selectors.d-block {
                        display: block !important;
                        width: 100%;
                    }

                </style>
                <style class="writing-ai-style">
                    .writing-essay-page .head-card {
                        margin: 3.2rem 0 0;
                    }

                    .writing-essay-page .head-card__contents {
                        display: flex;
                        gap: 32px;
                    }
                    .writing-essay-page .head-card__col {
                        flex: 1;
                    }.writing-essay-page .head-card.-sample .score-panel {
                        width: 100%;
                    }

                    .writing-essay-page .score-panel {
                        display: flex;
                        padding: var(--Space-xl, 24px);
                        flex-direction: column;
                        align-items: center;
                        gap: var(--Space-lg, 16px);
                        border-radius: var(--Space-xl, 24px);
                        border: 1px solid var(--Primary-primary-50, #EAECEF);
                        background: var(--Primary-primary-500, #294563);
                        color: #fff;
                    }.writing-essay-page .score-panel__title {
                        font-size: 16px;
                        font-weight: 400;
                        color: #fff;
                        text-align: center;
                        margin: 0;
                    }.writing-essay-page .score-panel__title strong {
                        font-size: 24px;
                        font-weight: 700;
                        display: block;
                        margin-top: 4px;
                    }.writing-essay-page .head-card.-sample .score-panel__scores {
                        flex-wrap: initial;
                    }
                    .writing-essay-page .score-panel__scores {
                        width: 100%;
                        display: flex;
                        flex-wrap: wrap;
                        gap: 8px;
                    }.writing-essay-page .head-card.-sample .score-panel__score-item.-big {
                        width: 50%;
                    }
                    .writing-essay-page .score-panel__score-item.-big {
                        width: 100%;
                    }
                    .writing-essay-page .score-panel__score-item {
                        border-radius: var(--Space-lg, 16px);
                        background: var(--System-White, #FFF);
                        display: flex;
                        padding: var(--Space-md, 8px) 24px;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        align-self: stretch;
                    }
                    .writing-essay-page .score-panel__score-item.-small {
                        flex: 1;
                    }
                    .writing-essay-page .score-panel__score-item.-big .score-panel__score-item-title {
                        font-size: 20px;
                    }
                    .writing-essay-page .score-panel__score-item-title {
                        font-size: 16px;
                        font-weight: 400;
                        color: #787878;
                    }
                    .writing-essay-page .score-panel__score-item.-big .score-panel__score-item-val {
                        font-size: 32px;
                        background: var(--Gradient-Writing, linear-gradient(180deg, #FAA859 0%, #BB7F44 100%));
                        background-clip: text;
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                    }
                    .writing-essay-page .score-panel__score-item-val {
                        font-size: 20px;
                        font-weight: 700;
                        font-family: "Montserrat", Helvetica, Arial, sans-serif;
                        color: #294563;
                    }

                    .evaluation__item-card--footer {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }

                    @media (max-width: 767px) {
                        .writing-essay-page .head-card.-sample .score-panel__scores {
                            flex-wrap: wrap;
                        }
                    }

                    @media (max-width: 767px) {
                        .writing-essay-page .head-card.-sample .score-panel__score-item.-big {
                            width: 100%;
                        }
                    }

                    

                    .final-score__comment.collapse.in {
                        display: flex !important;
                    }

                    .writing-essay-page .question-part .panel-heading a[aria-expanded="true"] {
                        background-color: #F9A95A;
                        color: #fff;
                    }

                    .writing-essay-page .part-score {
                        display: flex;
                        align-items: center;
                        gap: 16px;
                        justify-content: center;
                        font-family: "Montserrat", Helvetica, Arial, sans-serif;
                        color: #294563;
                        margin: 0 auto 2rem;
                    }

                    .writing-essay-page .part-score__text {
                        font-size: 16px;
                        font-weight: 700;
                    }

                    .writing-essay-page .part-score__val {
                        border-radius: 24.211px;
                        border: 0.807px solid #F9A95A;
                        background: #FFF;
                        display: flex;
                        padding: 3.228px 6.6rem;
                        justify-content: center;
                        align-items: center;
                        font-size: 24px;
                        font-weight: 700;
                    }  
                    
                    .writing-essay-page .evaluation__score {
                        width: 120px;
                        height: 46px;
                        line-height: 46px;
                        font-family: "Montserrat", Helvetica, Arial, sans-serif;
                        font-size: 20px;
                        font-weight: bold;
                        border-top-left-radius: 4px;
                        border-top-right-radius: 4px;
                        position: absolute;
                        right: 0;
                        color: #ffffff;
                        background-color: #294563;
                        text-align: center;
                        margin-top: -46px;
                    }

                    @media (max-width: 767px) {
                        .writing-essay-page .evaluation__score {
                            bottom: 90%;
                            width: auto;
                            min-width: 66px;
                            font-size: 16px;
                        }
                    }

                    /* for text editor */
                    .note-editable {
                        background: white !important;
                    }

                    .--show-contents {
                        display: block !important;
                    }

                    .badge-danger {
                        background-color: #a94442 !important;
                    }

                    del.-bb-orange {
                        border-bottom: 2px solid orange;
                        cursor: pointer;
                    }

                    span.correction.-bb-blue {
                        border-bottom: 2px solid #5bc2d2;
                        cursor: pointer;
                    }

                    .evaluate__anyway {
                        border: 1px solid #d9dcdf;
                        background-color: #fff !important;
                        border-radius: 4px;
                        padding: 24px;
                    }


                    .evaluate__anyway .nav-tabs {
                        border: none;
                        margin-bottom: 24px;
                    }

                    .evaluate__anyway .nav::before {
                        display: table;
                        content: " ";
                    }

                    .evaluate__anyway .nav-tabs li {
                        margin-right: 10px;
                    }
                    .evaluate__anyway .nav>li {
                        position: relative;
                        display: block;
                    }
                    .evaluate__anyway .nav-tabs>li {
                        float: left;
                        margin-bottom: -1px;
                    }
                    .evaluate__anyway li, .evaluate__anyway ul {
                        border: 0;
                        font-size: 100%;
                        font-style: inherit;
                        font-weight: inherit;
                        margin: 0;
                        outline: 0;
                        padding: 0;
                        vertical-align: baseline;
                    }

                    .evaluate__anyway .active .my-purchase__btn {
                        background-color: #294563;
                        color: #fff;
                    }
                    .evaluate__anyway .my-purchase__btn {
                        border-radius: 4px;
                        border: none;
                        height: 38px;
                        padding: 0 16px;
                        font-size: 14px;
                        background-color: #d4dae0;
                        color: #294563;
                        width: auto;
                        min-width: 80px;
                    }

                    .evaluate__anyway .no-answer {
                        background-color: #f5f5f5;
                        max-width: 100%;!;!i;!;
                        padding: 26px;
                        border-radius: 10px;
                    }

                    .evaluate__anyway form.proqyz__post-evaluation-form .row {
                        background: #f5f5f5;
                    }

                </style>
            </head>
            <?php return ob_get_clean();
        } else {
            ob_start(); ?>
            <head>
                <?php if(isset($this->quiz->title)) { ?>
                <title><?php echo $this->quiz->title; ?> | Quiz</title>    
                <?php } ?>

                <meta charset="<?php bloginfo('charset'); ?>" />    
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link rel="stylesheet" href="<?php echo site_url() . '/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/css/polyfixes.css'; ?>" />
                <?php echo $this->dynamic_css_imports(); ?>
                <?php echo $this->dynamic_js_imports_header(); ?>
                
                <link rel="stylesheet" href="<?php echo site_url();?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/css/proqyz-quiz.css" />
                <?php echo $this->get_writing_styles(); ?>
                <script>
                    const fullMockTest  = `<?php echo $this->is_fullmock_test? 'true' : 'false'; ?>`;
                </script>
            </head>
            <?php return ob_get_clean();
        }

    }

    public function footer(){

        if( $this->result ){
            // data-toggle="sp-collapse" 
            ob_start(); ?>
            <script id="session-details" type="application/json">
                <?php 
                    echo json_encode( (object) [
                        "_id"           => $this->_id,
                        "category"      => $this->category,
                        "time"          => $this->default,
                        "post_id"       => $this->post_id,
                        "session"       => $this->session
                    ]); 
                ?>
            </script>
            <script id="quiz-json" type="application/json">
                <?php echo json_encode( (object) $this->_q); ?>
            </script>

            <?php if( $this->resume && $this->user_data !== null ) { 
                $answers = isset($this->user_data->answers)? (object) $this->user_data->answers : [];
            ?>
            <script id="user-responses" type="application/json">
                <?php 
                    echo json_encode( (object) [
                        "answers"   => $answers
                    ]); 
                ?>
            </script>
            <?php } ?>

            <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js" integrity="sha512-zMfrMAZYAlNClPKjN+JMuslK/B6sPM09BGvrWlW+cymmPmsUT1xJF3P4kxI3lOh9zypakSgWaTpY6vDJY/3Dig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js" type="text/javascript"></script>
            <script>
                jQuery(document).ready(function($){
                    $(document).on('click','[data-toggle="sp-collapse"]', function(event){
                        event.preventDefault();
                        let targetId = $(this).attr('href');
                        let aside       = false;
                        $('[data-toggle="sp-collapse"]').each(function(index,element){
                            let citem       = $(element);
                            let cTargetId   = citem.attr('href');
                            let eIndex      = citem.data('item-key');
                            
                            if(citem.attr('href') == targetId) {
                                if($(this).hasClass('collapsed')){
                                    $(`[data-common-tab="task-${eIndex}"]`).click();
                                    aside = true;
                                    
                                    $(this).removeClass('collapsed').attr('aria-expanded', 'true');
                                    $(targetId).addClass('collpasing');
                                    $(targetId).addClass('in');
                                    $(targetId).attr('aria-expanded', 'true');
                                    $(targetId).css('height','');


                                    // review box
                                    
                                    /*
                                    $(`[data-common-tab="task-${eIndex}"]`).addClass('active');
                                    $(`[data-common-tab="task-${eIndex}"]`).parent().addClass('active');
                                    $(`[data-common-tab="task-${eIndex}"]`).attr('aria-selected', 'true');
                                    $(`[data-common-tab-content="task-${eIndex}"]`).css('display', 'block');
                                    $(`[data-common-tab-content="task-${eIndex}"]`).addClass('show');
                                    $(`[data-common-tab-content="task-${eIndex}"]`).addClass('in');

                                    */

                                    /*
                                    aside = true;
                                    setTimeout(() => {
                                        $(targetId).removeClass('collapsing');
                                    }, 333);

                                    */
                                } else {
                                    
                                    $(this).addClass('collapsed').attr('aria-expanded', 'false');
                                    $(targetId).removeClass('in');
                                    $(targetId).css('height','0');
                                    $(targetId).attr('aria-expanded', 'false');
                                    //$('body').removeClass('aside-enable');
                                    

                                    /*
                                    $(`[data-common-tab="task-${eIndex}"]`).removeClass('active');
                                    $(`[data-common-tab="task-${eIndex}"]`).parent().removeClass('active');
                                    $(`[data-common-tab="task-${eIndex}"]`).attr('aria-selected', 'false');
                                    $(`[data-common-tab-content="task-${eIndex}"]`).css('display', 'none');
                                    $(`[data-common-tab-content="task-${eIndex}"]`).removeClass('show');
                                    $(`[data-common-tab-content="task-${eIndex}"]`).removeClass('in');

                                    */
                                }
                            } else {
                                citem.addClass('collapsed').attr('aria-expanded', 'false');
                                $(cTargetId).removeClass('in');
                                $(cTargetId).css('height','0');
                                $(cTargetId).attr('aria-expanded', 'false');

                                console.log(eIndex);

                                /*
                                $(`[data-common-tab="task-${eIndex}"]`).removeClass('active');
                                $(`[data-common-tab="task-${eIndex}"]`).parent().removeClass('active');
                                $(`[data-common-tab="task-${eIndex}"]`).attr('aria-selected', 'false');
                                $(`[data-common-tab-content="task-${eIndex}"]`).css('display', 'none');
                                $(`[data-common-tab-content="task-${eIndex}"]`).removeClass('show');
                                $(`[data-common-tab-content="task-${eIndex}"]`).removeClass('in');

                                */

                            }
                        });

                        if(aside){
                            $('body').addClass('aside-enable');
                        } else {
                            $('body').removeClass('aside-enable');
                        }

                    });

                    $(document).on('click', '[data-toggle="sp-tab"]', function(){
                        let isActive = $(this).attr('aria-selected');
                        let hasActiveClass = $(this).hasClass('active');
                        let parentActive   = $(this).parent().hasClass('active');
                        let cTarget        = $(this).data('target');
                        let cIndex         = $(this).data('item-key'); 
                        if(isActive && hasActiveClass && parentActive) {

                        } else {
                            $('[data-toggle="sp-tab"]').each(function(index,element){
                                let eTarget = $(element).data('target');
                                let eIndex  = $(element).data('item-key');
                                if( eTarget == cTarget ) {
                                    // show this
                                    $(element).addClass('active');
                                    $(element).parent().addClass('active');
                                    $(element).attr('aria-selected', 'true');
                                    $(eTarget).css('display', 'block');
                                    $(eTarget).addClass('show');
                                    $(eTarget).addClass('in');

                                    // review box
                                    $(`[data-common-panel="task-${eIndex}"]`).removeClass('collapsed').attr('aria-expanded', 'true');
                                    $(`[data-common-panel-content="task-${eIndex}"]`).addClass('collpasing');
                                    $(`[data-common-panel-content="task-${eIndex}"]`).addClass('in');
                                    $(`[data-common-panel-content="task-${eIndex}"]`).attr('aria-expanded', 'true');
                                    $(`[data-common-panel-content="task-${eIndex}"]`).css('height','');
                                    setTimeout(() => {
                                        $(`[data-common-panel-content="task-${eIndex}"]`).removeClass('collapsing');
                                    }, 333);


                                
                                    
                                } else {
                                    // hide
                                    $(element).removeClass('active');
                                    $(element).parent().removeClass('active');
                                    $(element).attr('aria-selected', 'false');
                                    $(eTarget).css('display', 'none');
                                    $(eTarget).removeClass('show');
                                    $(eTarget).removeClass('in');

                                    $(`[data-common-panel="task-${eIndex}"]`).addClass('collapsed').attr('aria-expanded', 'false');
                                    $(`[data-common-panel-content="task-${eIndex}"]`).removeClass('in');
                                    $(`[data-common-panel-content="task-${eIndex}"]`).attr('aria-expanded', 'false');
                                    $(`[data-common-panel-content="task-${eIndex}"]`).css('height','0');
                                    

                                }
                            });
                        }

                    });

                    $(document).on('click','.feedback-status_btn', function(){
                        if($(this).hasClass('show-notepad')){
                            $(this).removeClass('show-notepad');
                            $($(this).data('target')).removeClass('show');
                        } else {
                            $(this).addClass('show-notepad');
                            $($(this).data('target')).addClass('show');
                        }
                    });

                    // tab for teacher evaluation
                    $(document).on('click','.evaluate__anyway [data-evaluator-category]', function(){
                        var root = $(this).closest('.evaluate__anyway');
                        let evaluatorCategory = $(this).data('evaluator-category');
                        // hide all other category tabs and make them inactive
                        root.find('[data-evaluator-category]').removeClass('active');
                        // hide tabs
                        root.find('.tab__content').removeClass('--show-contents');

                        $(this).addClass('active');
                        root.find(`#${evaluatorCategory}-tab`).addClass('--show-contents');

                    });


                    $(document).on('submit', '.proqyz__post-evaluation-form', function(event){
                        event.preventDefault();
                        let sectionId       = $(this).find('#section-id');
                        let sectionOrder    = $(this).find('#section-order');
                        let resultId        = $(this).find('#result-id');
                        let feedbackContent = $(this).find('#feedback-content');
                        let score1          = $(this).find('#score-1');
                        let score2          = $(this).find('#score-2');
                        let score3          = $(this).find('#score-3');
                        let score4          = $(this).find('#score-4');
                        let btn             = $(this).find('[type="submit"]');
                        let notice          = $(this).find('.notice__area');

                        let tmp             = btn.html();

                        let scores = [
                            +score1.val(),
                            +score2.val(),
                            +score3.val(),
                            +score4.val()
                        ];
                        
                        

                        let report = {
                            "review_content":       feedbackContent.val(),
                            "scores":               scores,
                            "section_id":           sectionId.val(),
                            "section_order":        sectionOrder.val(),
                            "result_id":            resultId.val()
                        };


                        window[sectionId] = $.ajax({
                            url: "/wp-admin/admin-ajax.php",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: "proqyz-submit-writing-review",
                                report: report
                            },
                            beforeSend: function(){
                                if(window[sectionId] != null){
                                    window[sectionId].abort();
                                }
                                btn.attr('disabled', true);
                                btn.html("Posting...");
                                notice.html('Posting...');

                            },
                            success: function(response){
                                if(response.success){
                                    notice.html(`<p class="text-success">Review Posted successfully</p>`);
                                    if(response.success?.reload){
                                        window.location.reload();
                                    }
                                }

                                if(response.error){
                                    notice.html(`<p class="text-danger">${response.error.message}</p>`);
                                }

                                btn.attr('disabled', false);
                                btn.html(tmp);
                                

                                setTimeout(() => {
                                    notice.html("");    
                                }, 2000);
                            },
                            error: function(err){
                                notice.html(`<p class="text-danger">${err.message}</p>`);
                                btn.attr('disabled', false);
                                btn.html(tmp);
                                setTimeout(() => {
                                    notice.html("");    
                                }, 2000);
                            }
                        });
                        
                    });

                    // remove review
                    $(document).on('click', '.review__remove', function(ev){
                        ev.preventDefault();
                        var closestForm     = $(this).closest('form.proqyz__post-evaluation-form');
                        let sectionId       = closestForm.find('#section-id');
                        let sectionOrder    = closestForm.find('#section-order');
                        let resultId        = closestForm.find('#result-id');
                        let reviewId        = closestForm.find('#review-id');
                        let submitBtn       = closestForm.find('[type="submit"]');
                        let notice          = closestForm.find('.notice__area');
                        let deleteBtn       = $(this);

                        console.log(closestForm);
                        window[sectionId] = $.ajax({
                            url: "/wp-admin/admin-ajax.php",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: "proqyz-remove-writing-review",
                                review_id: reviewId.val()
                            },
                            beforeSend: function(){
                                if(window[sectionId] != null){
                                    window[sectionId].abort();
                                }
                                submitBtn.attr('disabled', true);
                                deleteBtn.attr('disabled', true);
                                notice.html('Removing...');

                            },
                            success: function(response){
                                if(response.success){
                                    notice.html(`<p class="text-success">Review removed successfully</p>`);
                                    window.location.reload();
                                }

                                if(response.error){
                                    notice.html(`<p class="text-danger">${response.error.message}</p>`);
                                }

                                submitBtn.attr('disabled', false);
                                deleteBtn.attr('disabled', false);
                                
                                

                                setTimeout(() => {
                                    notice.html("");    
                                }, 2000);
                            },
                            error: function(err){
                                notice.html(`<p class="text-danger">${err.message}</p>`);
                                submitBtn.attr('disabled', false);
                                deleteBtn.attr('disabled', false);
                                
                                setTimeout(() => {
                                    notice.html("");    
                                }, 2000);
                            }
                        });


                    });

                    // reset the evaluation
                    $(document).on('submit', '.proqyz__reset-evaluation', function(event) {
                        event.preventDefault();
                        let sectionId       = $(this).find('#section-id');
                        let resultId        = $(this).find('#result-id');
                        let notice          = $(this).find('.notice__area');
                        let btn             = $(this).find('[type="submit"]');

                        let confirm = window.confirm("Are you sure you want to reset evaluation?");
                        if( !confirm ) { return; }

                        window[sectionId] = $.ajax({
                            url: "/wp-admin/admin-ajax.php",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: "proqyz-reset-evaluation",
                                section_id: sectionId.val(),
                                result_id:  resultId.val(),
                            },
                            beforeSend: function(){
                                if(window[sectionId] != null){
                                    window[sectionId].abort();
                                }
                                btn.attr('disabled', true);
                            },
                            success: function(response){
                                if(response.success){
                                    notice.html(`<div class="alert alert-success">${response?.success?.message || 'Evaluation reset successfully'}</p>`);
                                    window.location.reload();
                                }

                                if(response.error){
                                    notice.html(`<div class="alert alert-danger">${response?.error?.message || 'Failed to reset evaluation'}</p>`);
                                }

                                btn.attr('disabled', false);
                                
                            },
                            error: function(err){
                                notice.html(`<div class="alert alert-danger">${err?.message || 'Failed to reset evaluation'}</p>`);
                                btn.attr('disabled', false);
                                
                            }
                        });
                    });

                    $(document).on('submit', '.proqyz__reevaluation-request' , function(event) {
                        event.preventDefault();
                        let sectionId       = $(this).find('#section-id');
                        let resultId        = $(this).find('#result-id');
                        let reason          = $(this).find('#reason');
                        let notice          = $(this).find('.notice__area');
                        let btn             = $(this).find('[type="submit"]');

                        window[sectionId] = $.ajax({
                            url: "/wp-admin/admin-ajax.php",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: "proqyz-submit-reevaluation-request",
                                section_id: sectionId.val(),
                                result_id:  resultId.val(),
                                reason: reason.val()
                            },
                            beforeSend: function(){
                                if(window[sectionId] != null){
                                    window[sectionId].abort();
                                }
                                btn.attr('disabled', true);
                            },
                            success: function(response){
                                if(response.success){
                                    notice.html(`<div class="alert alert-success">${response?.success?.message || 'Reason posted successfully'}</p>`);
                                    
                                }

                                if(response.error){
                                    notice.html(`<div class="alert alert-danger">${response?.error?.message || 'Failed to post reason'}</p>`);
                                }

                                btn.attr('disabled', false);
                                
                            },
                            error: function(err){
                                notice.html(`<div class="alert alert-danger">${err?.message || 'Failed to post reason'}</p>`);
                                btn.attr('disabled', false);
                                
                            }
                        });


                    });

                    $(document).on('change', '.task-li', function(event) {
                        let rootId = $(this).data('holder');
                        $(rootId).html($(this).val());
                    });

                    $(document).on('click','#modal-task-pay [data-type="tasks"]', (event) => {
                        $('.service-price-box__service-name').html(event.target.dataset.taskname);
                        $('.service-price-box__service-task').html(event.target.dataset.taskLabel);
                        $('.service-price-box__item-price').html(event.target.dataset.price);
                        $('.service-price-box__item-price.-en').html(event.target.dataset.price);
                        $('#selected-task').val(event.target.dataset.slug);
                        $('#selected-task-id').val(event.target.dataset.taskId);
                        
                        $('#modal-task-pay [data-type="tasks"]').removeClass('-active');
                        event.target.classList.add('-active');
                        // $('.tasks__selectors').removeClass('d-block');
                        
                    });

                    $(document).on('submit', '#st-proqyz-ieltslms-evaluation-cart-form', (event) => {
                        event.preventDefault();
                        let resultId = $('#st-proqyz-ieltslms-evaluation-cart-form #selected-result-id');
                        let task     = $('#st-proqyz-ieltslms-evaluation-cart-form #selected-task');
                        let taskId   = $('#st-proqyz-ieltslms-evaluation-cart-form #selected-task-id');
                        const tmp    = $('#modal-notification-payment .modal-detail').html();
                        $('#modal-notification-payment .modal-detail').removeClass('d-none');
                        $('#modal-notification-payment .error_detail').addClass('d-none');

                        window['ajax-add-to-cart'] = $.ajax({
                            url: `<?php echo admin_url('admin-ajax.php'); ?>`,
                            type: 'post',
                            data: {
                                "action"        : "ST_PROQYZ_IELTSLMS_EVALUATION_ADD_TO_CART",
                                "id"            : resultId.val(),
                                "task"          : task.val(),
                                "task_id"       : taskId.val(),
                                "category"      : 'writing',
                                "url"           : window.location.href
                            },
                            beforeSend: function () {

                                // $('body').addClass('ajax-cart-loading');   
                                if(window['ajax-add-to-cart'] != null) window['ajax-add-to-cart'].abort(); 
                                // $('.ajax-cart-notice').html(`<div style="color:#ffa64e;">Adding to cart in progress</div>`);    
                                $('#modal-notification-payment').modal('show');
                            
                            },
                            success: function (json) {
                                if(json.success) {
                                    // $('.ajax-cart-notice').html(`<div class="text-success">${json.success.message}</div>`);
                                    $('#modal-notification-payment .modal-detail').html(`
                                        <h3 class="modal-caption">
                                            ${json.success.message}
                                        </h3>
                                    `);
                                    if(json.success.has_redirect) {
                                        window.location.href = json.success.redirect_url;
                                    }
                                } else if(json.error){
                                    // $('.ajax-cart-notice').html(`<div style="color:#fff3f2;">${json.error.message}</div>`);
                                    $('#modal-notification-payment .error_detail').html(`
                                        <i class="fa fa-exclamation-triangle fa-2x" aria-hidden="true"></i>
                                        <h3 class="modal-caption">
                                            Error: ${json.error.message}
                                        </h3>
                                        <div class="modal-action">
                                            <button class="cancel-ajax-cart" data-dismiss="modal">Cancel</button>
                                        </div>
                                    `);
                                    $('#modal-notification-payment .modal-detail').addClass('d-none');
                                    $('#modal-notification-payment .error_detail').removeClass('d-none');
                                } else {
                                    // $('.ajax-cart-notice').html(`<div style="color:#fff3f2;">Uknown response</div>`);
                                    $('#modal-notification-payment .error_detail').html(`
                                        <i class="fa fa-exclamation-triangle fa-2x" aria-hidden="true"></i>
                                        <h3 class="modal-caption">
                                            Error: Unknown Response
                                        </h3>
                                        <div class="modal-action">
                                            <button class="cancel-ajax-cart" data-dismiss="modal">Cancel</button>
                                        </div>
                                    `);
                                    $('#modal-notification-payment .modal-detail').addClass('d-none');
                                    $('#modal-notification-payment .error_detail').removeClass('d-none');
                                }   
                            },
                            error: function(){
                                // $('.ajax-cart-notice').html(`<div style="color:#fff3f2;">Process failed, Please cancel and try again </div>`); 
                                $('#modal-notification-payment .error_detail').html(`
                                    <i class="fa fa-exclamation-triangle fa-2x" aria-hidden="true"></i>
                                    <h3 class="modal-caption">
                                        Error: Process failed, Please cancel and try again 
                                    </h3>
                                    <div class="modal-action">
                                        <button class="cancel-ajax-cart" data-dismiss="modal">Cancel</button>
                                    </div>
                                `);
                                $('#modal-notification-payment .modal-detail').addClass('d-none');
                                $('#modal-notification-payment .error_detail').removeClass('d-none');
                            }
                        });


                    });

                    $(document).on('click','.cancel-ajax-cart', (event) => {
                        $('body').removeClass('ajax-cart-loading');
                        if(window['ajax-add-to-cart'] != null){
                            window['ajax-add-to-cart'].abort();
                        }




                    });

                    $(document).on('click', '[data-toggle="evaluation-plans"]', function(){
                        $('.evaluation__tabs-content').removeClass('--selected-plan');
                        $('.evaluation__tabs-li').removeClass('evaluation__plan--selected');
                        $(this).addClass('evaluation__plan--selected');
                        $($(this).data('target')).addClass('--selected-plan');
                        if(
                            $($(this).data('target')).find('.modal-task-pay__item').first().length > 0
                        ) {
                            $($(this).data('target')).find('.modal-task-pay__item:not(.disabled)').first().click(); 
                        }
                    });

                    $(document).on('submit', '.generate-ai-evaluation', function(event) {
                        event.preventDefault(); 
                        let btn             = $(this).find('[type="submit"]');
                        let resultId        = $(this).find('[name="result_id"]').val();
                        let sectionId       = $(this).find('[name="section_id"]').val();
                        let sectionOrder    = $(this).find('[name="section_order"]').val();
                        let notice          = $(this).find('.notice__area');
        
                        
                        $.ajax({
                            url: "/wp-admin/admin-ajax.php",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: "proqyz__writing-ai-evaluation",
                                result_id: resultId,
                                section_id: sectionId,
                                section_order: sectionOrder,
                                as: 'student'
                            },
                            beforeSend: function(){
                                btn.attr('disabled', true);
                                notice.html(`<div class="alert alert-info"><b>Processing Evaluation:</b>: This may take a few minutes. You can leave this tab, and we’ll notify you by email once the evaluation is complete.</p>`);
                            },
                            success: function(response){
                                btn.attr('disabled', false);
                                if( response?.success ) {
                                    notice.html(`<div class="alert alert-success">Reloading tab</p>`);
                                    window.location.reload();
                                }

                                if( response?.error ) {
                                    notice.html(`<div class="alert alert-danger">${response?.error?.message || 'Request Failed, please reload this tab'}</p>`);
                                }

                            },
                            error: function(err){
                                btn.attr('disabled', false);
                                notice.html(`<div class="alert alert-danger">${err?.message || 'Request Failed, please reload this tab'}</p>`);
                            }
                        });

                        
                    });

                    $(document).on('submit','.generate-ai-evaluation__as-teacher', function(event) {
                        event.preventDefault(); 
                        let btn             = $(this).find('[type="submit"]');
                        let resultId        = $(this).find('[name="result_id"]').val();
                        let sectionId       = $(this).find('[name="section_id"]').val();
                        let sectionOrder    = $(this).find('[name="section_order"]').val();
                        let notice          = $(this).find('.notice__area');
                        let closesetRoot    = $(this).closest('.evaluate__anyway');
                        
                        $.ajax({
                            url: "/wp-admin/admin-ajax.php",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: "proqyz__writing-ai-evaluation",
                                result_id: resultId,
                                section_id: sectionId,
                                section_order: sectionOrder,
                                as: 'teacher'
                            },
                            beforeSend: function(){
                                btn.attr('disabled', true);
                                notice.html(`<div class="alert alert-info"><b>Processing Evaluation:</b>: This may take a few minutes. You can leave this tab, and we’ll notify you by email once the evaluation is complete.</p>`);
                                closesetRoot.find('[type="submit"]').attr('disabled', true);
                            },
                            success: function(response){
                                btn.attr('disabled', false);
                                closesetRoot.find('[type="submit"]').attr('disabled', false);
                                if( response?.success ) {
                                    notice.html(`<div class="alert alert-success">Reloading tab</p>`);
                                    window.location.reload();
                                }

                                if( response?.error ) {
                                    notice.html(`<div class="alert alert-danger">${response?.error?.message || 'Request Failed, please reload this tab'}</p>`);
                                }

                            },
                            error: function(err){
                                btn.attr('disabled', false);
                                closesetRoot.find('[type="submit"]').attr('disabled', false);
                                notice.html(`<div class="alert alert-danger">${err?.message || 'Request Failed, please reload this tab'}</p>`);
                            }
                        });
                    });

                    $(document).on('click', '[data-toggle="toggle-visibility"]', function(){
                        var target = $(this).data('target');
                        $(target).toggleClass('--show-contents');
                    })


                    /* if(document.querySelectorAll(`#modal-task-pay [data-type="tasks"].avilable`).length > 0){
                        document.querySelectorAll(`#modal-task-pay [data-type="tasks"].avilable`)[0].click();
                    } else {
                        if(document.querySelectorAll(`#modal-task-pay [data-type="tasks"]`).length > 0){
                            document.querySelectorAll(`#modal-task-pay [data-type="tasks"]`)[0].click();
                        }
                    } */

                    if($('[data-toggle="evaluation-plans"]').length > 0 ) {
                        $('[data-toggle="evaluation-plans"]').first().click();
                    }




                    $('.summernote').summernote();
                });
            </script>
            
            <?php return ob_get_clean();
        } else {


            ob_start(); ?>
            <script id="session-details" type="application/json">
                <?php 
                    echo json_encode( (object) [
                        "_id"           => $this->_id,
                        "category"      => $this->category,
                        "time"          => $this->default,
                        "post_id"       => $this->post_id,
                        "session"       => $this->session
                    ]); 
                ?>
            </script>
            <script id="quiz-json" type="application/json">
                <?php echo json_encode( (object) $this->_q); ?>
            </script>

            <?php if( $this->resume && $this->user_data !== null ) { 
                $answers = isset($this->user_data->answers)? (object) $this->user_data->answers : [];
            ?>
            <script id="user-responses" type="application/json">
                <?php 
                    echo json_encode( (object) [
                        "answers"   => $answers
                    ]); 
                ?>
            </script>
            <?php } ?>

            <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js" integrity="sha512-zMfrMAZYAlNClPKjN+JMuslK/B6sPM09BGvrWlW+cymmPmsUT1xJF3P4kxI3lOh9zypakSgWaTpY6vDJY/3Dig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js" integrity="sha512-NhRZzPdzMOMf005Xmd4JonwPftz4Pe99mRVcFeRDcdCtfjv46zPIi/7ZKScbpHD/V0HB1Eb+ZWigMqw94VUVaw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/split.js/1.6.2/split.min.js" integrity="sha512-to2k78YjoNUq8+hnJS8AwFg/nrLRFLdYYalb18SlcsFRXavCOTfBF3lNyplKkLJeB8YjKVTb1FPHGSy9sXfSdg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
            <script src="<?php echo site_url();?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/js/uuidv4.js"></script>
            <script src="<?php echo site_url();?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/js/texthighter.js"></script>
            <script src="<?php echo site_url();?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/js/reading.js" type="text/javascript"></script>
            <?php return ob_get_clean();
        }
    }





    public function seconds_to_hms( $seconds ) {
        // Format the seconds into an H:i:s string
        $formatted_time = gmdate('H:i:s', $seconds);
    
        // If the formatted time starts with "00:", remove it
        if (strpos($formatted_time, '00:') === 0) {
            $formatted_time = substr($formatted_time, 3);
        }
    
        return $formatted_time;
    }

    public function countWords($input) {
        // Use preg_match_all to find all non-whitespace sequences
        preg_match_all('/\S+/', $input, $matches);
    
        // $matches[0] contains an array of all matched words
        return count($matches[0]);
    }

    /**
     * changes @1.2.1.2
     */
    public function dynamic_css_imports() {
        $site_url = site_url();
        ob_start(); ?>
        <style>
            @font-face {
                font-family: "Glyphicons Halflings";
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/glyphicons-halflings-regular.eot);
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/glyphicons-halflings-regular.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/glyphicons-halflings-regular.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/glyphicons-halflings-regular.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/glyphicons-halflings-regular.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular)
                    format("svg");
            }

            @font-face {
                font-family: "FontAwesome";
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.eot?v=4.7.0);
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.eot#iefix&v=4.7.0)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.woff2?v=4.7.0) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.woff?v=4.7.0) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.ttf?v=4.7.0) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.svg?v=4.7.0#fontawesomeregular)
                    format("svg");
                font-weight: normal;
                font-style: normal;
            }

            @font-face {
                font-family: "Ionicons";
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/ionicons.eot?v=2.0.1);
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/ionicons.eot?v=2.0.1#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/ionicons.ttf?v=2.0.1) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/ionicons.woff?v=2.0.1) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/ionicons.svg?v=2.0.1#Ionicons) format("svg");
                font-weight: normal;
                font-style: normal;
            }

            @font-face {
                font-family: "FontAwesome";
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.eot);
                src: local("FontAwesome"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/fontawesome-webfont.svg#fontawesomeregular)
                    format("svg");
                font-weight: normal;
                font-style: normal;
                font-display: swap;
            }

            @font-face {
                font-family: "Nunito";
                font-style: normal;
                font-weight: 300;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-300.eot);
                src: local("Nunito Light"), local("Nunito-Light"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-300.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-300.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-300.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-300.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-300.svg#Nunito) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Nunito";
                font-style: normal;
                font-weight: 400;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-regular.eot);
                src: local("Nunito Regular"), local("Nunito-Regular"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-regular.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-regular.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-regular.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-regular.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-regular.svg#Nunito) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Nunito";
                font-style: italic;
                font-weight: 400;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-italic.eot);
                src: local("Nunito Italic"), local("Nunito-Italic"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-italic.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-italic.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-italic.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-italic.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-italic.svg#Nunito) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Nunito";
                font-style: normal;
                font-weight: 600;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-600.eot);
                src: local("Nunito SemiBold"), local("Nunito-SemiBold"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-600.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-600.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-600.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-600.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-600.svg#Nunito) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Nunito";
                font-style: normal;
                font-weight: 700;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-700.eot);
                src: local("Nunito Bold"), local("Nunito-Bold"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-700.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-700.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-700.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-700.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-700.svg#Nunito) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Nunito";
                font-style: normal;
                font-weight: 800;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-800.eot);
                src: local("Nunito ExtraBold"), local("Nunito-ExtraBold"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-800.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-800.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-800.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-800.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-800.svg#Nunito) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Nunito";
                font-style: normal;
                font-weight: 900;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-900.eot);
                src: local("Nunito Black"), local("Nunito-Black"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-900.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-900.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-900.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-900.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/nunito-v14-latin-900.svg#Nunito) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Montserrat";
                font-style: normal;
                font-weight: 300;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-300.eot);
                src: local("Montserrat Light"), local("Montserrat-Light"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-300.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-300.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-300.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-300.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-300.svg#Montserrat) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Montserrat";
                font-style: normal;
                font-weight: 400;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-regular.eot);
                src: local("Montserrat Regular"), local("Montserrat-Regular"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-regular.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-regular.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-regular.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-regular.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-regular.svg#Montserrat)
                    format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Montserrat";
                font-style: normal;
                font-weight: 500;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-500.eot);
                src: local("Montserrat Medium"), local("Montserrat-Medium"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-500.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-500.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-500.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-500.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-500.svg#Montserrat) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Montserrat";
                font-style: italic;
                font-weight: 400;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-italic.eot);
                src: local("Montserrat Italic"), local("Montserrat-Italic"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-italic.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-italic.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-italic.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-italic.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-italic.svg#Montserrat)
                    format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Montserrat";
                font-style: normal;
                font-weight: 600;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-600.eot);
                src: local("Montserrat SemiBold"), local("Montserrat-SemiBold"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-600.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-600.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-600.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-600.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-600.svg#Montserrat) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Montserrat";
                font-style: normal;
                font-weight: 700;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-700.eot);
                src: local("Montserrat Bold"), local("Montserrat-Bold"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-700.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-700.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-700.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-700.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-700.svg#Montserrat) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Montserrat";
                font-style: normal;
                font-weight: 800;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-800.eot);
                src: local("Montserrat ExtraBold"), local("Montserrat-ExtraBold"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-800.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-800.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-800.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-800.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-800.svg#Montserrat) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "Montserrat";
                font-style: normal;
                font-weight: 900;
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-900.eot);
                src: local("Montserrat Black"), local("Montserrat-Black"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-900.eot#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-900.woff2) format("woff2"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-900.woff) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-900.ttf) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/montserrat-v15-latin-900.svg#Montserrat) format("svg");
                font-display: swap;
            }

            @font-face {
                font-family: "iot-fonts";
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/iot-fonts.eot?sex6nu=);
                src: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/iot-fonts.eot?sex6nu=#iefix)
                    format("embedded-opentype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/iot-fonts.ttf?sex6nu=) format("truetype"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/iot-fonts.woff?sex6nu=) format("woff"),
                    url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/fonts/iot-fonts.svg?sex6nu=#iot-fonts) format("svg");
                font-weight: normal;
                font-style: normal;
                font-display: block;
            }

            .practice-item__icon {
                display: inline-block;
                width: 34px;
                height: 34px;
                background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/shortcodes/course/default/dist/svgs/headphone-duotone.svg) center / contain no-repeat;
            }

            .practice-item__icon.-reading {
                background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/shortcodes/course/default/dist/svgs/notepad.svg);
            }

            .practice-item__icon.-writing {
                background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/shortcodes/course/default/dist/svgs/pennibstraight.svg);
            }

            .practice-item__icon.-speaking {
                background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/shortcodes/course/default/dist/svgs/microphone.svg);
            }

            .reading-test-result a span.icon-locale {
                background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/reading/icon_locale.png);
            }

            .listening-test-result a span.icon-locale {
                background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/listening/icon_locale1.png);
            }



            @font-face {
            font-family: swiper-icons;
            src: url("data:application/font-woff;charset=utf-8;base64, d09GRgABAAAAAAZgABAAAAAADAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABGRlRNAAAGRAAAABoAAAAci6qHkUdERUYAAAWgAAAAIwAAACQAYABXR1BPUwAABhQAAAAuAAAANuAY7+xHU1VCAAAFxAAAAFAAAABm2fPczU9TLzIAAAHcAAAASgAAAGBP9V5RY21hcAAAAkQAAACIAAABYt6F0cBjdnQgAAACzAAAAAQAAAAEABEBRGdhc3AAAAWYAAAACAAAAAj//wADZ2x5ZgAAAywAAADMAAAD2MHtryVoZWFkAAABbAAAADAAAAA2E2+eoWhoZWEAAAGcAAAAHwAAACQC9gDzaG10eAAAAigAAAAZAAAArgJkABFsb2NhAAAC0AAAAFoAAABaFQAUGG1heHAAAAG8AAAAHwAAACAAcABAbmFtZQAAA/gAAAE5AAACXvFdBwlwb3N0AAAFNAAAAGIAAACE5s74hXjaY2BkYGAAYpf5Hu/j+W2+MnAzMYDAzaX6QjD6/4//Bxj5GA8AuRwMYGkAPywL13jaY2BkYGA88P8Agx4j+/8fQDYfA1AEBWgDAIB2BOoAeNpjYGRgYNBh4GdgYgABEMnIABJzYNADCQAACWgAsQB42mNgYfzCOIGBlYGB0YcxjYGBwR1Kf2WQZGhhYGBiYGVmgAFGBiQQkOaawtDAoMBQxXjg/wEGPcYDDA4wNUA2CCgwsAAAO4EL6gAAeNpj2M0gyAACqxgGNWBkZ2D4/wMA+xkDdgAAAHjaY2BgYGaAYBkGRgYQiAHyGMF8FgYHIM3DwMHABGQrMOgyWDLEM1T9/w8UBfEMgLzE////P/5//f/V/xv+r4eaAAeMbAxwIUYmIMHEgKYAYjUcsDAwsLKxc3BycfPw8jEQA/gZBASFhEVExcQlJKWkZWTl5BUUlZRVVNXUNTQZBgMAAMR+E+gAEQFEAAAAKgAqACoANAA+AEgAUgBcAGYAcAB6AIQAjgCYAKIArAC2AMAAygDUAN4A6ADyAPwBBgEQARoBJAEuATgBQgFMAVYBYAFqAXQBfgGIAZIBnAGmAbIBzgHsAAB42u2NMQ6CUAyGW568x9AneYYgm4MJbhKFaExIOAVX8ApewSt4Bic4AfeAid3VOBixDxfPYEza5O+Xfi04YADggiUIULCuEJK8VhO4bSvpdnktHI5QCYtdi2sl8ZnXaHlqUrNKzdKcT8cjlq+rwZSvIVczNiezsfnP/uznmfPFBNODM2K7MTQ45YEAZqGP81AmGGcF3iPqOop0r1SPTaTbVkfUe4HXj97wYE+yNwWYxwWu4v1ugWHgo3S1XdZEVqWM7ET0cfnLGxWfkgR42o2PvWrDMBSFj/IHLaF0zKjRgdiVMwScNRAoWUoH78Y2icB/yIY09An6AH2Bdu/UB+yxopYshQiEvnvu0dURgDt8QeC8PDw7Fpji3fEA4z/PEJ6YOB5hKh4dj3EvXhxPqH/SKUY3rJ7srZ4FZnh1PMAtPhwP6fl2PMJMPDgeQ4rY8YT6Gzao0eAEA409DuggmTnFnOcSCiEiLMgxCiTI6Cq5DZUd3Qmp10vO0LaLTd2cjN4fOumlc7lUYbSQcZFkutRG7g6JKZKy0RmdLY680CDnEJ+UMkpFFe1RN7nxdVpXrC4aTtnaurOnYercZg2YVmLN/d/gczfEimrE/fs/bOuq29Zmn8tloORaXgZgGa78yO9/cnXm2BpaGvq25Dv9S4E9+5SIc9PqupJKhYFSSl47+Qcr1mYNAAAAeNptw0cKwkAAAMDZJA8Q7OUJvkLsPfZ6zFVERPy8qHh2YER+3i/BP83vIBLLySsoKimrqKqpa2hp6+jq6RsYGhmbmJqZSy0sraxtbO3sHRydnEMU4uR6yx7JJXveP7WrDycAAAAAAAH//wACeNpjYGRgYOABYhkgZgJCZgZNBkYGLQZtIJsFLMYAAAw3ALgAeNolizEKgDAQBCchRbC2sFER0YD6qVQiBCv/H9ezGI6Z5XBAw8CBK/m5iQQVauVbXLnOrMZv2oLdKFa8Pjuru2hJzGabmOSLzNMzvutpB3N42mNgZGBg4GKQYzBhYMxJLMlj4GBgAYow/P/PAJJhLM6sSoWKfWCAAwDAjgbRAAB42mNgYGBkAIIbCZo5IPrmUn0hGA0AO8EFTQAA");
            font-weight: 400;
            font-style: normal;
            }
            .take-test .iot-option {
            display: inline-block;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3C!-- Generator: Adobe Illustrator 24.2.3, SVG Export Plug-In . SVG Version: 6.00 Build 0) --%3E%3Csvg version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='12px' height='4px' viewBox='0 0 17.5 9.5' style='enable-background:new 0 0 17.5 9.5;' xml:space='preserve'%3E%3Cstyle type='text/css'%3E .st0%7Bfill-rule:evenodd;clip-rule:evenodd;%7D%0A%3C/style%3E%3Cg id='Arrow_x2F_Chevrons'%3E%3Cpath fill='%23294563' id='Vector__x28_Stroke_x29_' class='st0' d='M0.2,0.2c0.3-0.3,0.8-0.3,1.1,0l7.5,7.5l7.5-7.5c0.3-0.3,0.8-0.3,1.1,0 s0.3,0.8,0,1.1l-8,8C9,9.6,8.5,9.6,8.2,9.3l-8-8C-0.1,1-0.1,0.5,0.2,0.2z'/%3E%3C/g%3E%3C/svg%3E%0A")
                right 10px center no-repeat;
            width: 100px;
            height: 32px;
            padding: 0px 10px;
            padding-right: 20px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            font-size: 14px;
            color: #282828;
            border-radius: 100px;
            border: 1px solid var(--primary-primary-100, #bdc5cf);
            -webkit-transition: border-color ease-in-out 0.15s;
            -o-transition: border-color ease-in-out 0.15s;
            transition: border-color ease-in-out 0.15s;
            }
            .take-test .gutter.gutter-vertical {
            cursor: ns-resize !important;
            background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAFCAYAAABSIVz6AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAsSURBVChTYzxz5sx/BiAwNjZmBNFnz56lC58JRAwEYPz/H+wAuoORFtQMDADGbkHLElXbCwAAAABJRU5ErkJggg==");
            }
            .reading-footer .rf-bar .expand {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_expand.png) center no-repeat scroll;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 11px;
            }
            .reading-footer .rf-bar .rf-bar-time:before {
            content: "";
            position: absolute;
            top: 8px;
            left: 0;
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/time_icon.png);
            width: 30px;
            height: 30px;
            margin: 0px 2px 0px 0px;
            display: inline-block;
            }
            .reading-footer .rf-bar .rf-bar-pallete .pallete-title:before {
            content: "";
            position: absolute;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/pallete_icon.png) center no-repeat scroll;
            width: 20px;
            height: 20px;
            background-size: 20px;
            margin: 2px 5px 0px 0px;
            display: inline-block;
            position: relative;
            top: 4px;
            }
            .reading-footer .rf-content .close-rf {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_expand.png) center no-repeat;
            width: 20px;
            height: 20px;
            display: block;
            cursor: pointer;
            position: absolute;
            top: 7px;
            right: 15px;
            z-index: 999;
            opacity: 0.9;
            }
            .reading-footer .box-palleted .bp-bar .pbb-caption span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/pallete_icon.png) center no-repeat;
            width: 20px;
            height: 20px;
            background-size: 20px;
            margin: 2px 10px 0px 0px;
            display: inline-block;
            position: relative;
            top: 2px;
            }
            .reading-footer .question-panel .qp-caption span em {
            float: left;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_pallete.png) no-repeat center center;
            width: 20px;
            height: 20px;
            margin: 5px 5px 0px 0px;
            }
            .reading-footer .rfc-button a.question-pl {
            background: #f3f3f3 url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_quest_pallete.png) 5px center
                no-repeat scroll;
            background-size: 16px;
            }
            .reading-footer .rfc-button a.review span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_review1.png) center no-repeat scroll;
            width: 20px;
            height: 20px;
            }
            .reading-footer .rfc-button a.solution span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_lego_green.png) center no-repeat
                scroll;
            width: 20px;
            height: 20px;
            }
            .reading-footer .rfc-button a.submit span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_submit.png) center no-repeat scroll;
            width: 19px;
            height: 20px;
            }
            .reading-footer .rf-time .icon-time {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_time1.png) no-repeat center center;
            width: 52px;
            height: 56px;
            margin: 10px auto 0px auto;
            }
            .reading-footer .rf-button-wrap .rf-button span.icon-ask {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_ask2.png);
            }
            .reading-footer .rf-button-wrap .rf-button span.icon-review {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_review1.png);
            }
            .reading-footer .rf-button-wrap .rf-button span.icon-solution {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_lego2.png);
            }
            .reading-footer .rf-button-wrap .rf-button span.icon-submit {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_submit.png);
            }
            .reading-footer .rf-button-wrap .rf-button span.icon-pallete {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_pallete1.png);
            }
            .reading-footer.cyan .rf-button-wrap .rf-button span.icon-ask {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_ask4.png);
            }
            .reading-footer.cyan .rf-button-wrap .rf-button span.icon-review {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_review3.png);
            }
            .reading-footer.cyan .rf-button-wrap .rf-button span.icon-pallete {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_pallete2.png);
            }
            .reading-footer.cyan .rf-button-wrap .rf-button span.icon-solution {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_lego3.png);
            }
            .reading-footer.cyan .rf-button-wrap .rf-button span.icon-submit {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_submit1.png);
            }
            .reading-footer.cyan .rfc-button a.review span {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_review.png);
            width: 20px;
            height: 20px;
            }
            .reading-footer.cyan .rfc-button a.solution span {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_lego_green.png);
            width: 20px;
            height: 20px;
            }
            .reading-footer.cyan .rfc-button a.submit span {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_submit1.png);
            width: 19px;
            height: 20px;
            }

            .show-palette .reading-footer .rf-bar .expand {
            right: 8px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_expandmb_down.png) center no-repeat
                scroll;
            }
            .show-palette .reading-footer .rf-content .close-rf {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_expandmb_down.png) center no-repeat
                scroll;
            right: -5px;
            }
            .show-palette .reading-footer .rfc-button .expand {
            position: absolute;
            top: -8px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_expand_down.png) center no-repeat;
            }
            div.listen-from-here span.icon-listen {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_listen1.png) center no-repeat;
            background-size: contain;
            width: 18px;
            height: 18px;
            display: inline-block;
            position: relative;
            top: 4px;
            margin: -1px 6px 0px 0px;
            }
            div.listen-from-here:hover span.icon-listen {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_listen1_mb.png) center no-repeat;
            background-size: contain;
            }
            .listening-page .end-the-test a.btn-submit span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_submit_listening.png) no-repeat center
                center;
            display: inline-block;
            position: relative;
            width: 19px;
            height: 20px;
            margin: 0px 8px 0px 0px;
            top: 5px;
            }
            .listening-page .end-the-test .report span {
            display: inline-block;
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/report_icon_grey.png);
            width: 12px;
            height: 12px;
            display: inline-block;
            position: relative;
            margin: 0px 5px 0px 0px;
            top: 1px;
            }
            .listening-test-page .reading-footer.cyan .rfc-time span.clock {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/time_icon.png) center no-repeat;
            width: 28px;
            height: 28px;
            display: block;
            margin: 0 auto;
            }
            .listening-test-page .reading-footer.cyan .rfc-button a.submit span {
            width: 16px;
            height: 17px;
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_submit_ls.png);
            background-size: contain;
            }
            .listening-test-page .reading-footer.cyan .rfc-button a.review span {
            width: 18px;
            height: 18px;
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_review_ls.png);
            background-size: contain;
            }
            .listening-test-page .reading-footer.cyan .rfc-button a.solution span {
            width: 18px;
            height: 18px;
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_lego_ls.png);
            background-size: contain;
            }
            .listening-test-page.show-palette .reading-footer .rfc-button a.question-pl {
                background: #f3f3f3 url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_quest_palletecyan.png) 5px
                center no-repeat scroll;
                background-size: 16px;
            }
            .listening-test-page.show-palette .reading-footer .rfc-button a.question-pl {
                background: #f3f3f3 url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_quest_palletecyan.png) 5px
                center no-repeat scroll;
                background-size: 16px;
                top: 0;
            }
            .box-tag .tag-icon {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_tag.png) no-repeat center center;
            width: 28px;
            height: 28px;
            display: inline-block;
            position: relative;
            top: 9px;
            }
            .tag-item .close-tag {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_close.png) no-repeat center center;
            width: 8px;
            height: 8px;
            position: relative;
            display: inline-block;
            margin: 0px -5px 0px 5px;
            top: 0px;
            }
            .mostview-item .mi-tag em {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_tag1.png) no-repeat center center;
            width: 14px;
            height: 14px;
            display: inline-block;
            position: relative;
            top: 2px;
            margin: 0px 4px 0px 0px;
            }
            .mostview-item .mi-view em {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_view.png) no-repeat center center;
            width: 16px;
            height: 12px;
            display: inline-block;
            position: relative;
            top: 2px;
            margin: 0px 4px 0px 0px;
            }
            .tab-test ul li a span.icon-alltest {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_alltest.png) no-repeat center center;
            width: 20px;
            height: 20px;
            display: inline-block;
            margin: 0px 4px 0px 0px;
            position: relative;
            top: 5px;
            }
            .tab-test ul li a span.icon-academic {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_academic.png) no-repeat center center;
            width: 21px;
            height: 18px;
            display: inline-block;
            margin: 0px 4px 0px 0px;
            position: relative;
            top: 5px;
            }
            .tab-test ul li a span.icon-general-traning {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_genral.png) no-repeat center center;
            width: 18px;
            height: 20px;
            display: inline-block;
            margin: 0px 4px 0px 0px;
            position: relative;
            top: 5px;
            }
            .searchbox button {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_search.png) no-repeat center center;
            width: 20px;
            height: 20px;
            position: absolute;
            top: 5px;
            left: 10px;
            border: none;
            outline: none;
            background-color: transparent;
            }
            .news-item .mi-tag em {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_tag1.png) no-repeat center center;
            width: 14px;
            height: 14px;
            display: inline-block;
            position: relative;
            top: 2px;
            margin: 0px 4px 0px 0px;
            }
            .da-wrap em {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_calendar1.png) no-repeat center center;
            display: block;
            width: 20px;
            height: 20px;
            position: absolute;
            left: 6px;
            top: 3px;
            z-index: 1;
            }
            .analytic-score .as-col p em {
            display: inline-block;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_alltest.png) no-repeat center center;
            width: 25px;
            height: 25px;
            position: relative;
            margin: 0px 4px 0px 0px;
            top: 4px;
            }
            .analytic-score .as-col p em.icon-chart {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_chart.png) no-repeat center center;
            }
            .analytic-score .as-col p em.icon-target {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_target.png) no-repeat center center;
            }
            .analytic-score .as-col p em.icon-time {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_time2.png) no-repeat center center;
            }
            .analytic-score .as-col p em.icon-cup {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_cup.png) no-repeat center center;
            }
            .ic-top .btn-show-performance {
            display: block;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_arrow_down.png) no-repeat center
                center;
            width: 14px;
            height: 14px;
            position: absolute;
            top: 15px;
            right: 5px;
            cursor: pointer;
            }
            .ic-top .ic-icon div,
            .ic-top .ic-icon span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_listen.png) no-repeat center center;
            width: 50px;
            height: 50px;
            margin: 0px auto;
            display: block;
            }
            .ic-top .ic-icon.reading div,
            .ic-top .ic-icon.reading span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_reading.png) no-repeat center center;
            }
            .ic-top .ic-icon.writing div,
            .ic-top .ic-icon.writing span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_writing.png) no-repeat center center;
            }
            .ic-top .ic-icon.speaking div,
            .ic-top .ic-icon.speaking span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_speaking.png) no-repeat center center;
            }
            .avatar-profile div {
            width: 100%;
            height: 110px;
            background-color: #d4dae0;
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_user2.png);
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 40px 40px;
            }
            .modal-submit .control-modal-button a span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_modal_submit.png) no-repeat center
                center;
            }
            .control-modal-button a span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_view_solution.png) no-repeat center
                center;
            width: 22px;
            height: 22px;
            display: inline-block;
            position: relative;
            top: 5px;
            margin: 0px 5px 0px 0px;
            }
            .control-modal-button a.btn-retake span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_flash.png) no-repeat center center;
            width: 16px;
            height: 22px;
            display: inline-block;
            position: relative;
            top: 5px;
            margin: 0px 5px 0px 0px;
            }
            .control-modal-button a.btn-continute span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_lego1.png) no-repeat center center;
            width: 22px;
            height: 22px;
            display: inline-block;
            position: relative;
            top: 5px;
            margin: 0px 5px 0px 0px;
            }
            .control-modal-button a.btn-cancel span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_cancel_button.png) no-repeat left
                center;
            width: 18px;
            height: 18px;
            display: inline-block;
            position: relative;
            top: 4px;
            margin: 0px 5px 0px 0px;
            }
            .modal-auto .iot-modal-wrapper .ion-android-close:before {
            content: "";
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_cancel_button.png) no-repeat left
                center;
            width: 18px;
            height: 18px;
            }
            .bp-control-time-left a span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_submit2.png) no-repeat center center;
            width: 22px;
            height: 22px;
            display: inline-block;
            position: relative;
            top: 5px;
            margin: 0px 5px 0px 0px;
            }
            .bp-control-time-left a.btn-retake span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_flash.png) no-repeat center center;
            width: 16px;
            height: 22px;
            display: inline-block;
            position: relative;
            top: 5px;
            margin: 0px 5px 0px 0px;
            }
            .bp-control-time-left a.btn-continute span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_lego1.png) no-repeat center center;
            width: 22px;
            height: 22px;
            display: inline-block;
            position: relative;
            top: 5px;
            margin: 0px 5px 0px 0px;
            }
            .context-menu .cm-item.cm-pink {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/fill1.png);
            }
            .context-menu .cm-item.cm-green {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/fill2.png);
            }
            .context-menu .cm-item.cm-blue {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/fill3.png);
            }
            .context-menu .cm-item.cm-translate {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_translate.png);
            }
            .context-menu .cm-item.cm-delete {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/fill_delete.png);
            }
            .menu-board ul li a span.mb-icon1 {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_analytics.png);
            }
            .menu-board ul li a span.mb-icon2 {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_email.png);
            }
            .menu-board ul li a span.mb-icon3 {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_history.png);
            }
            .menu-board ul li a span.mb-icon4 {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_user1.png);
            }
            .menu-board ul li a span.mb-icon5 {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_cart.png);
            }
            .tt-sample-essay .panel-title > a {
            display: block;
            padding: 14px 30px 12px;
            font-size: 16px;
            font-weight: bold;
            color: #f9a95a;
            background: #f3f3f3;
            font-family: Nunito, sans-serif;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_expand_orange.png) right 30px center
                no-repeat;
            -moz-transition: all ease 0.12s;
            -o-transition: all ease 0.12s;
            -webkit-transition: all ease 0.12s;
            transition: all ease 0.12s;
            }
            .tt-sample-essay .panel-title > a[aria-expanded="true"] {
            color: #fff;
            background: #faa859 url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_expand.png) right 30px center
                no-repeat;
            }
            .service-intro {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/s_bg1.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: bottom center;
            padding: 20px 0px 170px 0px;
            text-align: center;
            margin: 0px 0px 40px 0px;
            position: relative;
            }
            .service-bottom {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/s_bg2.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: top center;
            padding: 200px 0px 90px 0px;
            text-align: center;
            margin: 30px 0px 0px 0px;
            }
            .filter-menu .filter-title:after {
            content: "";
            width: 14px;
            height: 8px;
            position: absolute;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/arrow_expand_module.png) center no-repeat;
            top: 6px;
            right: -24px;
            }
            .test-collection-page .test-des p .icon-ts.icon-calendar {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_date_orange.png);
            }
            .test-collection-page .test-des p .icon-ts.icon-view {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_view_orange.png);
            }
            .my-check .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 0;
            top: 1px;
            width: 100%;
            height: 100%;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/tick.png) center 1px no-repeat scroll;
            }
            .playback {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/audio-back.png) no-repeat center center;
            bottom: -34px;
            left: 7.2%;
            height: 24px;
            width: 24px;
            border: 0;
            cursor: pointer;
            display: block;
            font-size: 0;
            line-height: 0;
            overflow: hidden;
            padding: 0;
            position: absolute;
            text-decoration: none;
            z-index: 1;
            }
            .playforward {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/audio-next.png) no-repeat center center;
            bottom: -34px;
            left: 14.7%;
            height: 24px;
            width: 24px;
            border: 0;
            cursor: pointer;
            display: block;
            font-size: 0;
            line-height: 0;
            overflow: hidden;
            padding: 0;
            position: absolute;
            text-decoration: none;
            z-index: 1;
            }
            .mejs__button > button {
            background: #5bc3d2 url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_audio_play.png) center
                no-repeat;
            border: 0;
            cursor: pointer;
            display: block;
            font-size: 0;
            height: 41px;
            line-height: 0;
            margin: 0;
            overflow: hidden;
            padding: 0;
            position: absolute;
            text-decoration: none;
            width: 96px;
            }
            .mejs__button.mejs__volume-button > button {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_volume.png) center no-repeat scroll;
            border: 0;
            cursor: pointer;
            display: block;
            font-size: 0;
            height: 20px;
            width: 21px;
            line-height: 0;
            margin: 0;
            overflow: hidden;
            padding: 0;
            position: absolute;
            text-decoration: none;
            }
            .mejs__pause > button {
            background: #5bc3d2 url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_pause.png) center no-repeat;
            }
            .mejs__replay > button {
            background: #5bc3d2 url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_replay.png) center no-repeat;
            }
            .notifi-popup-mobile .notifications-popup-wp .close-button {
            position: absolute;
            width: 24px;
            height: 24px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_close_note.png) center no-repeat;
            right: 15px;
            top: 15px;
            padding: 0;
            cursor: pointer;
            }
            .notifi-popup-mobile .notifications-popup-wp .title.active span:before {
            content: "";
            position: absolute;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_arrow_back.png) no-repeat center
                center;
            width: 8px;
            height: 14px;
            left: -14px;
            top: 3px;
            }
            .messages-popup-mobile .close-button {
            position: absolute;
            width: 24px;
            height: 24px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_close_note.png) center no-repeat;
            right: 15px;
            top: 15px;
            padding: 0;
            cursor: pointer;
            }
            .messages-popup-mobile .mes-title-top.active span:before {
            content: "";
            position: absolute;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_arrow_back.png) no-repeat center
                center;
            width: 8px;
            height: 14px;
            left: -14px;
            top: 4px;
            }
            .messages-popup-mobile .table-chat-content .write-message button {
            width: 24px;
            height: 24px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_mess_submit.png) center no-repeat;
            position: absolute;
            right: 20px;
            top: 15px;
            left: auto;
            border: none;
            }
            .messages-page .table-chat-content .write-message button {
            width: 24px;
            height: 24px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_mess_submit.png) center no-repeat;
            position: absolute;
            right: 20px;
            top: 15px;
            left: auto;
            }
            .footer .bootstrap-select .caret {
            width: 14px;
            height: 8px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/arrow_expand_module.png) center no-repeat;
            border: none;
            margin-top: -4px;
            -moz-transition: all ease 0.2s;
            -o-transition: all ease 0.2s;
            -webkit-transition: all ease 0.2s;
            transition: all ease 0.2s;
            }
            .media-player .mejs__button > button {
            background: transparent url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/mejs-controls.svg);
            border: 0;
            cursor: pointer;
            display: block;
            font-size: 0;
            height: 20px;
            line-height: 0;
            margin: 11px 6px 10px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            text-decoration: none;
            width: 20px;
            z-index: 10;
            }
            .media-player .mejs__play > button {
            background: transparent url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/video-player-bt.svg) center
                no-repeat;
            background-size: 13px auto;
            z-index: 10;
            }
            .media-player .mejs__pause > button {
            background: transparent url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/media-pause-icon.svg) center
                no-repeat;
            }
            .media-player .mejs__replay > button {
            width: 16px;
            height: 16px;
            background: transparent url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/media-replay.svg) center
                no-repeat;
            background-size: contain;
            }
            .media-player .mejs__button.mejs__volume-button > button {
            width: 22px;
            height: 22px;
            background: transparent url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon-sound.svg) center
                no-repeat;
            }
            .media-player .mejs__fullscreen-button > button {
            background: transparent url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/fullscreen.svg) center
                no-repeat;
            width: 22px;
            height: 16px;
            background-size: contain;
            }
            .media-player .mejs__captions-button > button {
            background: transparent url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/caption-icon.svg) center
                no-repeat;
            width: 30px;
            height: 15px;
            background-size: contain;
            }
            .media-player .mejs__cinema > button {
            background: transparent url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/cinema-view.svg) center
                no-repeat;
            background-size: contain;
            width: 27px;
            height: 13px;
            }
            .media-player .mejs__playback > button {
            background: transparent url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/play-back-icon.svg) center
                no-repeat;
            background-size: contain;
            }
            .media-player .mejs__playforward > button {
            background: transparent url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/play-forward-icon.svg) center
                no-repeat;
            background-size: contain;
            }
            .profile-alert .container {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/attention.svg) left 15px center no-repeat;
            background-size: 23px auto;
            padding-left: 55px;
            }
            .system-alert .alert-content {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/attention.svg) left 10px center no-repeat;
            background-size: 18px auto;
            padding-left: 35px;
            }
            .intending-box .mbmn-tab[data-target="#intending"] {
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/boarding-pass-icon.svg) center top
                35px no-repeat;
            background-size: 80px;
            }
            .intending-box .mbmn-tab[data-target="#intending"].open {
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/boarding-pass-active-icon.svg) center
                top 35px no-repeat;
            background-size: 80px;
            border-top: 2px solid #32b4c8;
            border-radius: 0;
            }
            .intending-box .mbmn-tab[data-target="#not-intending"] {
            margin-top: 15px;
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/tutorial-icon.svg) center top 35px
                no-repeat;
            background-size: 80px;
            }
            .intending-box .mbmn-tab[data-target="#not-intending"].open {
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/tutorial-active-icon.svg) center top
                35px no-repeat;
            background-size: 80px;
            border-top: 2px solid #32b4c8;
            border-bottom: 2px solid #32b4c8;
            border-radius: 0;
            }
            .intending-box .intending-abroad > li > a:hover[href="#intending"] {
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/boarding-pass-active-icon.svg) center
                top 81px no-repeat;
            background-size: 110px;
            }
            .intending-box .intending-abroad > li > a:hover[href="#not-intending"] {
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/tutorial-active-icon.svg) center top
                86px no-repeat;
            background-size: 100px;
            }
            .intending-box .intending-abroad > li.active a[href="#intending"] {
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/boarding-pass-active-icon.svg) center
                top 81px no-repeat;
            background-size: 110px;
            }
            .intending-box .intending-abroad > li.active a[href="#not-intending"] {
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/tutorial-active-icon.svg) center top
                86px no-repeat;
            background-size: 100px;
            }
            .intending-box a[href="#intending"] {
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/boarding-pass-icon.svg) center top
                81px no-repeat;
            background-size: 110px;
            }
            .intending-box a[href="#not-intending"] {
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/tutorial-icon.svg) center top 86px
                no-repeat;
            background-size: 100px;
            }
            .intending-box .prompt li {
            padding-left: 35px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/check-list-icon.svg) left top no-repeat;
            background-size: 20px;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.5;
            color: #282828;
            margin-bottom: 25px;
            }
            .intending-box .jcarousel-control-prev,
            .intending-box .jcarousel-control-next {
            top: 29px;
            text-indent: -99999px;
            border: none;
            border-radius: 0;
            box-shadow: none;
            width: 23px;
            height: 23px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/profile-arrow-down.png) center no-repeat;
            background-size: contain;
            }
            .intending-box .destination .star {
            display: inline-block;
            width: 18px;
            height: 18px;
            margin-right: 10px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/star-inactive.svg) center no-repeat;
            background-size: contain;
            cursor: pointer;
            }
            .intending-box .destination .star.active {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/star-active.svg) center no-repeat;
            background-size: contain;
            }
            .bt-reg.join-free:before {
            content: "";
            display: inline-block;
            width: 18px;
            height: 18px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon-share.svg) center no-repeat;
            background-size: contain;
            margin-right: 9px;
            position: relative;
            top: 4px;
            }
            .wbn-listing .owl-carousel .owl-nav button.owl-prev,
            .wbn-listing .owl-carousel .owl-nav button.owl-next,
            .my-webinar .owl-carousel .owl-nav button.owl-prev,
            .my-webinar .owl-carousel .owl-nav button.owl-next,
            .wbn-detail .owl-carousel .owl-nav button.owl-prev,
            .wbn-detail .owl-carousel .owl-nav button.owl-next {
            font-size: 0;
            position: absolute;
            left: -56px;
            top: 50%;
            width: 20px;
            height: 35px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/wbn-arrow-left.png) center no-repeat;
            background-size: contain;
            transform: translateY(-50%);
            border: none;
            }
            .wbn-listing .owl-carousel .owl-nav button.owl-prev:hover,
            .wbn-listing .owl-carousel .owl-nav button.owl-next:hover,
            .my-webinar .owl-carousel .owl-nav button.owl-prev:hover,
            .my-webinar .owl-carousel .owl-nav button.owl-next:hover,
            .wbn-detail .owl-carousel .owl-nav button.owl-prev:hover,
            .wbn-detail .owl-carousel .owl-nav button.owl-next:hover {
            outline: none;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/wbn-arrow-left.png) center no-repeat;
            background-size: contain;
            }
            .wbn-listing .owl-carousel .owl-nav button.owl-next,
            .my-webinar .owl-carousel .owl-nav button.owl-next,
            .wbn-detail .owl-carousel .owl-nav button.owl-next {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/wbn-arrow-right.png) center no-repeat;
            background-size: contain;
            right: -56px;
            top: 50%;
            left: auto;
            }
            .wbn-listing .owl-carousel .owl-nav button.owl-next:hover,
            .my-webinar .owl-carousel .owl-nav button.owl-next:hover,
            .wbn-detail .owl-carousel .owl-nav button.owl-next:hover {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/wbn-arrow-right.png) center no-repeat;
            background-size: contain;
            }
            .test-bt:before {
            content: "";
            display: inline-block;
            width: 18px;
            height: 18px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_flash.png) center no-repeat;
            background-size: contain;
            margin-right: 5px;
            position: relative;
            top: 4px;
            }
            .explain-bt:before {
            content: "";
            display: inline-block;
            width: 16px;
            height: 16px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_resume.png) center no-repeat;
            background-size: contain;
            margin-right: 5px;
            position: relative;
            top: 4px;
            }
            .iot-cbx input:checked ~ .checkmark:after {
            display: block;
            background: #32b4c8 url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/iot-cb-icon.svg) center no-repeat;
            background-size: contain;
            }
            .iot-cbx-factor .checkmark:before {
            content: "";
            position: absolute;
            width: 13px;
            height: 20px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bullet-dice.svg) center no-repeat;
            background-size: contain;
            left: 10px;
            top: 9px;
            }
            .iot-detail-page .end-the-test a.btn-submit span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_submit_listening.png) no-repeat center
                center;
            display: inline-block;
            position: relative;
            width: 19px;
            height: 20px;
            margin: 0px 8px 0px 0px;
            top: 5px;
            }
            .iot-detail-page .end-the-test .report span {
            display: inline-block;
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/report_icon_grey.png);
            width: 12px;
            height: 12px;
            display: inline-block;
            position: relative;
            margin: 0px 5px 0px 0px;
            top: 1px;
            }
            .icon-modal-close {
            width: 17px;
            height: 17px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/close-modal.svg) center no-repeat;
            background-size: contain;
            display: block;
            position: absolute;
            top: -15px;
            right: -22px;
            cursor: pointer;
            -moz-transition: all ease 0.2s;
            -o-transition: all ease 0.2s;
            -webkit-transition: all ease 0.2s;
            transition: all ease 0.2s;
            }
            div.iti-flag {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/flags.png);
            }
            div.intl-tel-input .selected-flag div.iti-arrow {
            position: absolute;
            right: 7px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/profile-arrow-down.png) center no-repeat;
            background-size: contain;
            border: none !important;
            -moz-transition: all ease 0.2s;
            -o-transition: all ease 0.2s;
            -webkit-transition: all ease 0.2s;
            transition: all ease 0.2s;
            }
            .answer-slot.demo:after {
            content: "";
            width: 21px;
            height: 22px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/cursor.png) center no-repeat;
            background-size: contain;
            position: absolute;
            left: 138px;
            top: -5px;
            }
            p.drag-question.style-2:after,
            p.drag-question.style-3:after {
            content: "";
            position: absolute;
            width: 26px;
            height: 26px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/arrow-drop-test.png) center no-repeat;
            left: 50%;
            transform: translateX(-50%);
            top: 100%;
            background-size: contain;
            visibility: visible;
            }
            .answer-table.select-table td.active:after {
            content: "";
            position: absolute;
            width: 24px;
            height: 17px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/correct-icon.svg) center no-repeat;
            background-size: contain;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            }
            .wbn-bt.join-free:before {
            content: "";
            display: inline-block;
            width: 18px;
            height: 18px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon-share.svg) center no-repeat;
            background-size: contain;
            margin-right: 9px;
            position: relative;
            top: 4px;
            }
            .last-sale:before {
            content: "";
            display: inline-block;
            position: relative;
            top: 3px;
            width: 15px;
            height: 15px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon-cart.svg) center no-repeat;
            background-size: contain;
            margin-right: 4px;
            }
            .test-view:before,
            .test-date:before {
            content: "";
            display: inline-block;
            position: relative;
            top: 4px;
            width: 16px;
            height: 18px;
            margin: 0px 5px 0px 0px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_date_orange.png) center no-repeat;
            background-size: contain;
            }
            .test-date:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_view_orange.png);
            }
            .top-selling .best-item__new-tag:after,
            .top-selling .best-item__new-tag:before {
            content: "";
            position: absolute;
            display: inline-block;
            width: 4px;
            height: 7px;
            background: url(/themes/images/icons/corner-shadow-right.svg) center no-repeat;
            background-size: contain;
            top: 0;
            left: 100%;
            }
            .top-selling .best-item__new-tag:before {
            background-image: url(/themes/images/icons/corner-shadow-left.svg);
            left: initial;
            right: 100%;
            }
            .progress-animated .progress-bar,
            .progress-animated .bar {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/progressbar.gif) !important;
            filter: none;
            }
            .fileupload-processing .fileupload-process,
            .files .processing .preview {
            display: block;
            width: 32px;
            height: 32px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/loading.gif) center no-repeat;
            background-size: contain;
            }
            .study-abroad-box .bootstrap-select .dropdown-toggle .caret {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/profile-arrow-down.png) center no-repeat;
            background-size: contain;
            width: 26px;
            height: 26px;
            border: 0;
            padding: 0;
            top: 6px;
            margin-top: 0;
            right: 11px;
            }
            .consultation-box__list-item:before {
            content: "";
            display: inline-block;
            position: absolute;
            width: 16px;
            height: 16px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/check-list-icon-orange.svg) center
                no-repeat;
            background-size: contain;
            left: 0;
            top: 2px;
            }
            .test-header.-listening .volume__practice-title:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/listening-icon.svg);
            }
            .test-header.-writing .volume__practice-title:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/writing-icon.svg);
            }
            .test-header.-speaking .volume__practice-title:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/speaking-icon.svg);
            }
            .test-header .volume__practice-title:before {
            content: "";
            width: 40px;
            height: 40px;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            margin: 0 auto;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/reading-icon.svg) center no-repeat;
            background-size: contain;
            }
            .test-header__btn-info:before {
            content: "";
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_arrowus_down.png) no-repeat center
                center;
            width: 14px;
            height: 14px;
            display: inline-block;
            position: relative;
            margin: 0px 5px 0px 0px;
            top: 3px;
            transform: rotate(0deg);
            }
            .mega-menu__menu-item.-parent > a:after {
            content: "";
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_arrowus_down.png) no-repeat center
                center;
            width: 14px;
            height: 14px;
            display: inline-block;
            position: absolute;
            top: 50%;
            margin-top: -7px;
            transform: rotate(0deg);
            right: 15px;
            -moz-transition: all ease 0.2s;
            -o-transition: all ease 0.2s;
            -webkit-transition: all ease 0.2s;
            transition: all ease 0.2s;
            }
            .mega-menu__menu-icon.-ask {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_ask.png);
            }
            .mega-menu__menu-icon.-share {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_share.png);
            }
            .mega-menu__menu-icon.-report {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_report.png);
            }
            .mega-menu__menu-icon.-text {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_text_size.png);
            }
            .mega-menu__menu-icon.-print {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_print.png);
            }
            .mega-menu__menu-icon.-download {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_download.png);
            }
            .mega-menu__menu-icon.-instruction {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_introduction.png);
            }
            .mega-menu__menu-icon.-exit {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_exit.png);
            }
            .mega-menu__menu-icon.-save {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_save_draft.png);
            }
            .question-board__item.-green:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/howto-answer-green-icon.svg);
            }
            .question-board__item.-listen-here:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/listening-icon.svg);
            width: 16px;
            height: 16px;
            }
            .question-board__item.-show-notepad:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/show-note-pad-icon.svg);
            }
            .question-board__item:before {
            content: "";
            display: inline-block;
            width: 18px;
            height: 18px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/howto-answer-icon.svg) center no-repeat;
            background-size: contain;
            vertical-align: middle;
            margin-right: 8px;
            position: relative;
            top: -1px;
            }
            .iot-pagination__item.-prev .iot-pagination__link,
            .iot-pagination__item.-next .iot-pagination__link {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/next-page-arrow.svg) center no-repeat;
            background-size: 15px;
            }
            .iot-pagination__item.-first .iot-pagination__link,
            .iot-pagination__item.-end .iot-pagination__link {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/end-page-arrow.svg) center no-repeat;
            background-size: 15px;
            }
            .popular-tips .tip-item__tag:before {
            content: "";
            display: inline-block;
            width: 13px;
            height: 13px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/tag-icon.svg) no-repeat center center;
            margin-right: 6px;
            }
            .popular-tips .tip-item__view:before {
            content: "";
            display: inline-block;
            width: 13px;
            height: 13px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/eye-icon.svg) no-repeat center center;
            margin-right: 6px;
            }
            .webform-submission-form .control-label.option input[type="checkbox"]:checked {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon-check-mark.svg) center no-repeat;
            background-size: 93% 83%;
            }
            .webform-submission-form input.form-checkbox[type="checkbox"]:checked {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon-check-mark.svg) center no-repeat;
            background-size: 93% 83%;
            }
            #modal-register-mobile .ion-android-close:before {
            content: "";
            width: 18px;
            height: 18px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/close-bt.svg) center no-repeat;
            background-size: contain;
            }
            #modal-edit-avatar .upload-box.uploading .cancel-upload {
            width: 12px;
            height: 12px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_cancel_button.png) center no-repeat;
            background-size: contain;
            position: absolute;
            right: -21px;
            top: -1px;
            cursor: pointer;
            }
            #modal-edit-avatar .upload-box.uploading.complete .finished-bt {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon-check.svg) center no-repeat;
            background-size: contain;
            width: 12px;
            height: 12px;
            position: absolute;
            right: -21px;
            top: -1px;
            }
            #modal-edit-avatar .upload-box.uploading.error .cancel-upload {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_cancel_orange.png) center no-repeat;
            }
            i.close-modal.-blue {
            opacity: 1;
            top: 15px;
            right: 15px;
            display: inline-block;
            width: 18px;
            height: 18px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/close-bt.svg) center no-repeat;
            background-size: contain;
            }
            .modal-gift-countdown .modal-dialog {
            max-width: 445px;
            padding: 26px 30px;
            background: #fff url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/pages/give-away/gift-modal-bg.svg)
                center no-repeat;
            background-size: contain;
            }
            .modal-gift-countdown__title:after {
            content: "";
            position: absolute;
            display: inline-block;
            width: 221px;
            height: 162px;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/pages/give-away/gift-countdown-title-bg.svg)
                center no-repeat;
            background-size: contain;
            z-index: -1;
            }
            .modal-rpmistake__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bugs-icon.svg) center no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .modal-rpmistake .iot-opselect .dropdown-toggle .caret {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_option_select.svg);
            width: 15px;
            height: 15px;
            top: 12px;
            transform: none;
            }
            .modal-credit-evaluation.-speaking .modal-content {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bg-credit-evaluation-pink.svg);
            }
            .modal-credit-evaluation .modal-content {
            padding: 20px 30px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bg-credit-evaluation-orange.svg) center
                no-repeat;
            background-size: cover;
            border: none;
            }
            .modal-choose-evaluation.-speaking .service-price-box {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bg-buy.svg);
            }
            .modal-choose-evaluation.-speaking .evaluation-widget__label:after,
            .modal-choose-evaluation.-speaking .evaluation-widget__label:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/corner-pink-shadow-right.svg);
            }
            .modal-choose-evaluation.-speaking .evaluation-widget__label:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/corner-pink-shadow-left.svg);
            }
            .modal-choose-evaluation .card-successful__new-tag:after,
            .modal-choose-evaluation .card-successful__new-tag:before {
            content: "";
            position: absolute;
            display: inline-block;
            width: 4px;
            height: 7px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/corner-shadow-right.svg) center
                no-repeat;
            background-size: contain;
            top: 0;
            left: 100%;
            }
            .modal-choose-evaluation .card-successful__new-tag:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/corner-shadow-left.svg);
            left: initial;
            right: 100%;
            }
            .modal-choose-evaluation .service-price-box {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bg-buy-writing.svg) center no-repeat;
            background-size: cover;
            border: none;
            padding: 20px;
            }
            .modal-choose-evaluation .evaluation-widget__label:after,
            .modal-choose-evaluation .evaluation-widget__label:before {
            content: "";
            position: absolute;
            display: inline-block;
            width: 4px;
            height: 7px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/corner-orange-shadow-right.svg)
                center no-repeat;
            background-size: cover;
            top: 0;
            left: 100%;
            }
            .modal-choose-evaluation .evaluation-widget__label:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/corner-orange-shadow-left.svg);
            left: initial;
            right: 100%;
            }
            .modal-choose-mode.listening .choose-box__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-settings.svg);
            }
            .modal-choose-mode.listening .choose-box__icon.-test-mode {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-computer.svg);
            }
            .modal-choose-mode.listening .choose-box__caption:before {
            content: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-idea.svg);
            }
            .modal-choose-mode.reading .choose-box__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-settings-reading.svg);
            }
            .modal-choose-mode.reading .choose-box__icon.-test-mode {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-computer-reading.svg);
            }
            .modal-choose-mode.reading .choose-box__caption:before {
            content: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-idea-reading.svg);
            }
            .modal-choose-mode.writing .choose-box__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-settings-writing.svg);
            }
            .modal-choose-mode.writing .choose-box__icon.-test-mode {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-computer-writing.svg);
            }
            .modal-choose-mode.writing .choose-box__caption:before {
            content: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-idea-writing.svg);
            }
            .modal-choose-mode.speaking .choose-box__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-settings-speaking.svg);
            }
            .modal-choose-mode.speaking .choose-box__icon.-test-mode {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-computer-speaking.svg);
            }
            .modal-choose-mode.speaking .choose-box__caption:before {
            content: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-idea-speaking.svg);
            }
            .modal-choose-mode .choose-box__icon {
            width: 50px;
            height: 50px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-settings.svg) center no-repeat;
            background-size: contain;
            align-self: center;
            margin: 0 0 5px;
            }
            .modal-choose-mode .choose-box__icon.-test-mode {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-computer.svg);
            }
            .modal-choose-mode .choose-box__caption:before {
            content: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-idea.svg);
            display: inline-block;
            margin-right: 1rem;
            }
            .modal-fulltest__caption:before {
            content: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/fulltest-idea.svg);
            display: inline-block;
            margin-right: 1rem;
            }
            .speaking-test .modal-view-solution__icon,
            .speaking-test .modal-submit-test__icon,
            .speaking-test .modal-exit-test__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-speaking-icon.svg);
            }
            .speaking-test .modal-time-up__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/timeup-icon-speaking.svg);
            }
            .speaking-test .modal-rpmistake__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bugs-speaking-icon.svg);
            }
            .writing-test .modal-view-solution__icon,
            .writing-test .modal-submit-test__icon,
            .writing-test .modal-exit-test__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-writing-icon.svg);
            }
            .writing-test .modal-time-up__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/timeup-icon-writing.svg);
            }
            .writing-test .modal-rpmistake__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bugs-writing-icon.svg);
            }
            .listening-test .modal-view-solution__icon,
            .listening-test .modal-submit-test__icon,
            .listening-test .modal-exit-test__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-listening-icon.svg);
            }
            .listening-test .modal-time-up__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/timeup-icon-listening.svg);
            }
            .listening-test .modal-rpmistake__icon {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bugs-listening-icon.svg);
            }
            .modal-submit-test__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-icon.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .modal-overdue__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-speaking-icon.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .modal-cancel-test__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-speaking-icon.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .modal-confirm__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-icon.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .modal-confirm__icon.-writing {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-writing-icon.svg);
            }
            .modal-confirm__icon.-speaking {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-speaking-icon.svg);
            }
            .modal-refusal__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-speaking-icon.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .modal-exit-test__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-icon.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .modal-view-solution__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-icon.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .modal-time-up__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-icon.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .modal-submit-email__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/submit-email-icon.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 1.6rem;
            }
            .modal-ai-timeup__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/timeup-icon-speaking.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .modal-tip-use__icon {
            width: 80px;
            height: 80px;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/choose-idea-speaking.svg) center
                no-repeat;
            background-size: contain;
            display: block;
            margin: 0 auto 2rem;
            }
            .list-answer li em.true {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_true.png) no-repeat center center !important;
            height: 10px !important;
            width: 14px !important;
            display: inline-block !important;
            margin: 0px 0px 0px 5px !important;
            }
            .list-answer li em.false {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_false.png) no-repeat center center !important;
            height: 10px !important;
            width: 14px !important;
            display: inline-block !important;
            margin: 0px 0px 0px 5px !important;
            }
            label.credit-card-label {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/CreditCardIcn.png);
            background-repeat: no-repeat;
            height: 99px;
            cursor: pointer;
            display: flex !important;
            padding: 0px 10px;
            align-items: center;
            justify-content: center;
            border: 1px solid #898989;
            border-radius: 3px;
            text-align: center;
            width: 100%;
            background-position: 50% 7%;
            background-size: 28%;
            content: "Debit/ Credit Card";
            font-size: 15px !important;
            color: #536b83 !important;
            padding-top: 55px;
            font-family: "Nunito" !important;
            font-weight: bolder !important;
            margin: 30px 0px 30px 0px !important;
            text-indent: -9999px;
            }
            label.paypal-label {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/PayPalIcn.png);
            background-repeat: no-repeat;
            background-position: center center;
            height: 99px;
            cursor: pointer;
            display: flex !important;
            padding: 0px 10px;
            align-items: center;
            justify-content: center;
            border: 1px solid #898989;
            border-radius: 3px;
            text-align: center;
            width: 100%;
            left: 50px;
            background-position: 50% 13%;
            background-size: 24%;
            font-size: 14px !important;
            color: #536b83 !important;
            padding-top: 55px;
            font-family: "Nunito" !important;
            font-weight: bolder !important;
            margin: 30px 0px 30px 0px !important;
            }
            .mastercard {
            background-image: url(http://i.imgur.com/SJbRQF7.png);
            }
            .test-hero .test-hero-icon.orange span {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_writing.png) no-repeat center center;
            }
            #modal-wt-vote .modal-wt-vote__capcha-refresh::before,
            #modal-voting .modal-voting__capcha-refresh::before {
            content: "";
            text-indent: 0;
            display: block;
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/refresh.svg) no-repeat center;
            height: 100%;
            }
            div#wechat-qr-code .status_succ .status_icon {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_popupwechat.png) 0 -46px no-repeat;
            display: inline-block;
            width: 38px;
            height: 38px;
            vertical-align: middle;
            }
            .hero.-australia {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/hero-detail-australia-bg.jpg);
            }
            .hero.-us {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/hero-detail-us-bg.jpg);
            }
            .hero.-ca {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/hero-detail-canada-bg.jpg);
            }
            .brush-line {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/pages/study-abroad/brush-line.svg) bottom
                center no-repeat;
            background-size: contain;
            }
            .notify-live-lesson-confirmation .how-to-td {
                display: flex;
                align-items: center;
                background: url("<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/table-border-bottom.png") bottom center
                no-repeat !important;
                padding: 6px 0;
            }
            .speaking-test-page .iot-cbx input:checked ~ .checkmark:after {
            display: block;
            background: #294563 url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/iot-cb-icon.svg) center no-repeat !important;
            background-size: contain;
            }
            .modal-refusal.full-test-ios .modal-refusal__icon {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/information-listening-icon.svg)
                center no-repeat;
            }
            .congratulation-page.-speaking .service-price-box {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bg-buy.svg) !important;
            }
            .congratulation-page .service-price-box {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bg-buy-writing.svg) center no-repeat !important;
            background-size: cover !important;
            border: none;
            padding: 24px;
            }


            @media (max-width: 767px) {
            .show-palette .reading-footer .rf-bar .expand {
                right: 8px;
                background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_expandmb_down.png) center no-repeat
                scroll;
            }

            }


            @media (max-width: 767px) {
            .profile-alert .container {
                background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/attention.svg) left 6px center no-repeat;
                background-size: 18px auto;
                padding-left: 30px;
            }
            }

            @media (max-width: 767px) {
            .system-alert .alert-content {
                background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/attention.svg) left 6px center no-repeat;
                background-size: 18px auto;
                padding-left: 50px;
            }
            }

            @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            div.iti-flag {
                background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/flags%402x.png);
            }
            }


            /* writing */
            .writing-essay-page .community-vote__average-score:after {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/arrow_expand_module.png) center no-repeat;
            }

            .writing-essay-page .final-score__comment-panel:after {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/arrow_expand_module.png) center no-repeat;
            }

            @media (max-width: 767px) {
            .writing-essay-page .evaluation .dropdown-toggle .caret {
                background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_option_select.svg) center no-repeat;
            }
            }


            .writing-essay-page .footer-banner {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/view-result-speaking-bg-banner.jpg) center
                no-repeat;
            }


            .writing-essay-page .vote-box {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/community-feedback-bg-orange.webp) center
                no-repeat;
            }


            .writing-essay-page .panel-title > a:after {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/arrow_expand_module.png) center no-repeat;
            }


            .writing-essay-page .share-box__btn-copy:before {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon-alltest-active.svg) center no-repeat;
            }

            .writing-essay-page .share-box__btn-copy:hover:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon-alltest.svg);
            }

            .writing-essay-page .test-box {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/background-writing-taketest.png) center
                no-repeat;
            }

            /* speaking */
            .speaking-review-page .vote-box {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/community-feedback-bg-pink.webp) center
                no-repeat;
            }


            .speaking-review-page .recording__collapse:after {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/arrow_expand_module.png) center no-repeat;
            }


            .speaking-review-page .recording__collapse:after {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/arrow_expand_module.png) center no-repeat;
            }

            .speaking-review-page .recording__question-title {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/speaking-explain-icon.svg) left 5px top
                12px no-repeat;
            }


            .speaking-review-page .test-box {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/green-chameleon.jpg) center no-repeat;
            }

            .speaking-review-page .card-successful.-speaking .service-price-box {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bg-buy.svg); 
            }


            .speaking-review-page .card-successful__new-tag:after,
            .speaking-review-page .card-successful__new-tag:before {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/corner-shadow-right.svg) center
                no-repeat;
            }

            .speaking-review-page .card-successful__new-tag:before {
            background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/corner-shadow-left.svg);
            }

            .speaking-review-page .service-price-box {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bg-buy-writing.svg) center no-repeat;
            }

            .speaking-review-page .final-score-box.-examiner {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/bg-buy.svg) center no-repeat;
            }


            .speaking-review-page .evaluation__title:after {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/arrow_expand_module_white.png) center
                no-repeat;
            }


            @media (max-width: 767px) {
            .speaking-review-page .evaluation .dropdown-toggle .caret {
                background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/icon_option_select.svg) center no-repeat;
            }
            }

            .speaking-review-page .footer-banner {
            background: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/icons/view-result-writing-bg-banner.jpg) center
                no-repeat;
            }




            
            

        </style>
        <?php return ob_get_clean();
    }
    /**
     * changes @1.2.1.2
     */
    public function dynamic_js_imports_header(){
        ob_start(); ?>
        <script>
            window.site_url = `<?php echo site_url(); ?>`;
        </script>
        <?php return ob_get_clean();
    }

    /**
     * Function to find object by key and value
     *
     * @param array $objectsArray - Array of objects to search in
     * @param string $key - The key to compare
     * @param mixed $value - The value to compare with
     * @return object|null - Returns the object if found, otherwise null
     */
    public function findObjectByKeyValue($objectsArray, $key, $value) {
        foreach ($objectsArray as $obj) {
            if (isset($obj->$key) && $obj->$key == $value) {
                return $obj;
            }
        }
        return null; // Return null if no object is found
    }

}



