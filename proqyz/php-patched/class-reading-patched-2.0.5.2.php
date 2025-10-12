<?php 

require_once 'class-questions.php';

class Reading extends Questions{
    public $post_id                 = null;
    public $student_id              = null;
    public $quiz                    = [];
    public $category                = '';
    public $_id                     = '';
    public $result_id               = '';
    public $sections                = [];
    public $questions               = [];
    public $seconds                 = 60;
    public $default                 = 3600;
    public $resume                  = false;
    public $result                  = false;
    public $solution                = false;
    public $session                 = [];
    public $user_data               = null;
    public $_q                      = [];
    public static $sections_html    = '';
    public static $questions_html   = '';
    public static $pallete          = '';
    public static $current_p_index  = 0;
    public static $p_counter        = 0;
    public static $review_boxes     = '';
    public static $rq_c             = 0;
    public $attempt_url             = '';
    public static $question_qt1      = [];
    public $is_fullmock_test        = false;
    


    public function __construct($params = []) {
        $params = (object) $params;
        $this->category     = isset($params->category) ? (string) $params->category : '';
        $this->_id          = isset($params->_id) ? (string) $params->_id : '';

        $this->sections     = isset($params->sections) ? (array) $params->sections : [];
        $this->questions    = isset($params->questions) ? (array) $params->questions : [];

        $this->quiz         = isset($params->quiz) ? (object) $params->quiz : (object) [];
        $time               = isset($this->quiz->time)? (object) $this->quiz->time : null;
        $quiz_settings      = isset($this->quiz->settings)? (object) $this->quiz->settings : null;
        $this->resume       = isset($params->resume) ? (bool) $params->resume : false;
        $this->result       = isset($params->result) ? (bool) $params->result : false;
        $this->solution     = isset($params->solution) ? (bool) $params->solution : false;
        $this->post_id      = isset($params->post_id) ? $params->post_id : null;
        $this->session      = isset($params->session)? (object) $params->session : [];
        $this->user_data    = isset($params->user_data)? (object) $params->user_data : null;
        $this->result_id    = isset($params->result_id)? (int) $params->result_id : null;  
        $this->_q           = isset($params->_q)? (object) $params->_q : (object) [];
        $this->attempt_url  = isset($params->attempt_url)? (string) $params->attempt_url : '';
        $this->student_id   = $params->student_id;
        
        if($quiz_settings !== null){
            $qus_loc = isset($quiz_settings->locate_question)? (bool) $quiz_settings->locate_question : false;
            $this->is_loc = $qus_loc;
        }
        
        if($time !== null){
            $minuts = isset($time->mm)? (int) $time->mm : 60;
            $this->default = $minuts * 60;
            if($this->default <= 0){
                $this->default = 60 * 60;
            }
        }

        if( $this->resume ) {
            $this->seconds      = isset($params->seconds)? (int) $params->seconds : $this->default;
            $this->set_user_data($this->user_data, $this->resume);
        } else if( $this->result ) {
            $this->seconds      = isset($params->seconds)? (int) $params->seconds : $this->default;
            $this->set_user_data($this->user_data, false, $this->result);

        } else {
            $this->seconds      = $this->default;
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
                $passage_content = (string) stripslashes($section->content);

                // replace text match using {[][]}
                preg_match_all( '#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im',$passage_content, $matches,PREG_SET_ORDER);
                foreach($matches as $k => $v){
                    $needed_txt = $v[1];
                    $exact_txt = $v[0];
                    $text_domain = '';    
                    if(preg_match_all( '#\[(.*?)\]#im', $needed_txt, $square_matches )){
                        
                        $question_id = isset($square_matches[1][1])? $square_matches[1][1] : '';
                        $question_txt = isset($square_matches[1][0])? $square_matches[1][0] : '';

                        $text_domain = '';
                        $text_domain .= $question_txt;
                        $text_domain .= '';

                        if($this->result){
                            if($question_id == "qe"){
                                $text_domain = '<span class="proqyz__explainqe proqyz__explainq ">';
                                $text_domain .= $question_txt;
                                $text_domain .= '</span>';
                            }else{
                                $text_domain = '<span data-no="Q'.$question_id.'" class="proqyz__explainq'.$question_id.' proqyz__explain ">';
                                $text_domain .= $question_txt;
                                $text_domain .= '</span>';
                            }
                        }

                        
                    }
                    
                    $passage_content = str_replace($exact_txt,$text_domain,$passage_content); 
                }
                
                // replace text match using |[][]|
                preg_match_all( '#\|(.*?)(?:\|(\d+))?(?:[\s]+)?\|#im',$passage_content, $matches,PREG_SET_ORDER);
                foreach($matches as $k => $v){
                    $needed_txt = $v[1];
                    $exact_txt = $v[0];

                    if(preg_match_all( '#\((.*?)\)#im', $needed_txt, $square_matches )){

                        $question_id = $square_matches[1][1];
                        $question_txt = $square_matches[1][0];

                        $text_domain = '';
                        $text_domain .= $question_txt;
                        $text_domain .= '';

                        if( $this->result ){
                            if($question_id == "qe"){
                                $text_domain = '<span class="proqyz__explainqe proqyz__explainq proqyz__inner">';
                                $text_domain .= $question_txt;
                                $text_domain .= '</span>';
                            }else{
                                $text_domain = '<span data-no="Q'.$question_id.'" class="proqyz__explainq'.$question_id.' proqyz__explain proqyz__inner">';
                                $text_domain .= $question_txt;
                                $text_domain .= '</span>';
                            }
                        }
                        
                    }

                    $passage_content = str_replace($exact_txt,$text_domain,$passage_content); 
                }

                
                
                # section html buffer module
                ob_start(); ?>
                <section 
                    id="part-<?php echo $skey+1; ?>" 
                    class='<?php echo $skey === 0 ? "test-contents ckeditor-wrapper -show" : "test-contents ckeditor-wrapper"; ?>' 
                    style="overflow-y:scroll;outline:none;display:none"
                >
                    <div class="test-contents__paragragh">
                        <?php echo $passage_content; ?>
                    </div>
                </section>
                <?php 
                self::$sections_html .= ob_get_clean();
                # clean and store the buffer and increment it
            }

            return self::$sections_html;
        }
    }

    /**
     * solution or Result sections : html
     */
    public function get_solution_sections_html(){
        if (count($this->sections) > 0) {
            foreach ($this->sections as $skey => $section) {
                # main section object
                $section = (object) $section;
                $title   = (string) $section->title;
                $content = (string) $section->content;

                # section html buffer module
                ob_start(); ?>
                <div 
                    id="set-container-<?php echo $skey+1; ?>" 
                    class='<?php echo $skey === 0 ? "tab-section-reading active" : "tab-section-reading hidden"; ?>' 
                >
                    <div class="passage-content">
                        <?php echo $content; ?>
                    </div>
                </div>
                <?php 
                self::$sections_html .= ob_get_clean();
                # clean and store the buffer and increment it
            }

            return self::$sections_html;
        }
    }

    public function get_question_content($question = []) {
        // main question
        $question_html          = "";
        $question               = (object) $question;
        $question_id            = isset($question->_id)? $question->_id : '';
        $_questionSettings      = isset($question->_questionSettings)? (object) $question->_questionSettings : (object) [];
        $input_type             = isset($_questionSettings->inputType)? (string) $_questionSettings->inputType : null;
        $_answerSettings        = isset($question->_answerSettings)? (object) $question->_answerSettings : (object) [];
        $number_of_options      = isset($_answerSettings->numberOfOptions)? (int) $_answerSettings->numberOfOptions : 0;
        $answerType             = isset($_answerSettings->answerType)? (string) $_answerSettings->answerType : null;
        $answerTypes            = isset($_answerSettings->answerTypes)? (array) $_answerSettings->answerTypes : (array) [];
        $questions              = isset($question->questions)? (array) $question->questions : [];

        if( $input_type === "fillup" ) {
            # if its fillup then get regex {}
            
            $modified_response  = (object) $this->question_type_fillup(self::$current_p_index, $question);
            $headings           = isset($modified_response->headings)? $modified_response->headings : '';
            $modified_html      = isset($modified_response->html)? $modified_response->html : '';
            $explanation_html   = isset($modified_response->explanations)? $modified_response->explanations : '';

            ob_start(); ?>
                <div class="test-panel__item" data-q_id="<?php echo $question_id; ?>" data-type="question-block">
                    <?php echo $headings; ?>
                    <div class="test-panel__answers-wrap">
                        <?php echo $modified_html; ?>           
                    </div>
                    <?php if($this->result){ ?>
                        <ul><?php echo $explanation_html; ?></ul>
                    <?php } ?>
                </div>
            <?php $question_html .= ob_get_clean();

        } else if( $input_type === "select" ) {
            
            $modified_response  = (object) $this->question_type_select(self::$current_p_index, $question);
            
            $headings           = isset($modified_response->headings)? $modified_response->headings : '';
            $modified_html      = isset($modified_response->html)? $modified_response->html : '';
            $explanation_html   = isset($modified_response->explanations)? $modified_response->explanations : '';

            ob_start(); ?>
                <div class="test-panel__item" data-q_id="<?php echo $question_id; ?>" data-type="question-block">
                    <?php echo $headings; ?>
                    <div class="test-panel__answers-wrap">
                        <?php echo $modified_html; ?>           
                    </div>
                    <?php if($this->result){ ?>
                        <ul><?php echo $explanation_html; ?></ul>
                    <?php } ?>
                </div>
            <?php $question_html .= ob_get_clean();
        } else if( $input_type === "radio" ) {
            $modified_response  = (object) $this->question_type_radio(self::$current_p_index, $question);
            $headings           = isset($modified_response->headings)? $modified_response->headings : '';
            $modified_html      = isset($modified_response->html)? $modified_response->html : '';

            ob_start(); ?>
                <div class="test-panel__item" data-q_id="<?php echo $question_id; ?>" data-type="question-block">
                    <?php echo $headings; ?>
                    <div class="test-panel__answers-wrap">
                        <?php echo $modified_html; ?>           
                    </div>
                </div>
            <?php $question_html .= ob_get_clean();
        } else if( $input_type === "checkbox" ) {
            $modified_response  = (object) $this->question_type_checkbox(self::$current_p_index, $question);
            $headings           = isset($modified_response->headings)? $modified_response->headings : '';
            $modified_html      = isset($modified_response->html)? $modified_response->html : '';
            $explanation_html   = isset($modified_response->explanations)? $modified_response->explanations : '';

            
            ob_start(); ?>
                <div class="test-panel__item" data-q_id="<?php echo $question_id; ?>" data-type="question-block">
                    <?php echo $headings; ?>
                    <div class="test-panel__answers-wrap">
                        <?php echo $modified_html; ?>           
                    </div>
                    <?php if($this->result){ ?>
                        <?php echo $explanation_html; ?>
                    <?php } ?>
                </div>
            <?php $question_html .= ob_get_clean();
        }
            
        return $question_html;

    }

    public function get_questions_html() {
        if (count($this->sections) > 0 && count($this->questions) > 0) {
            self::$questions_html = "";

            foreach ($this->sections as $skey => $section) {
                
                # main section object
                $section            = (object) $section;
                $title              = (string) $section->title;
                $_id                = (string) $section->_id;

                
                $filteredQuestions = (array) array_filter($this->questions, function ($obj) use ($_id) {
                    $question = (object) $obj;
                    return $question->_postId == $_id;
                });

                $section_questions_html = "";

                if(count($filteredQuestions) > 0) {
                    
                    foreach( $filteredQuestions as $qkey => $question ){
                        self::$current_p_index = $skey;
                        $question = (object) $question;
                        $section_questions_html .= $this->get_question_content($question);
                        
                    }

                }

                ob_start(); ?>
                    <section 
                        id="part-questions-<?php echo $skey+1; ?>" 
                        class='<?php echo ($skey === 0)? "test-panel -show" : "test-panel"; ?>' 
                        style="overflow-y:scroll;outline:none;display:none"
                    >
                        <div class="test-panel__header">
                            <h2 class="test-panel__title">Part <?php echo $skey+1; ?></h2>
                            <div class="test-panel__title-caption"></div>
                        </div>
                        <?php echo $section_questions_html; ?>
                    </section>
                <?php self::$questions_html .= ob_get_clean();

                self::$p_counter++;
                
            }

            return self::$questions_html;
            
        }
    }

    public function get_questions_pallete(){
        $total_q_counter    = 0;
        $report             = null;
        

        if( $this->result ) { $report = (object) $this->get_report(); }

        if (count($this->sections) > 0) {
            foreach ($this->sections as $skey => $section) {
                # main section object
                $section            = (object) $section;
                $title              = (string) $section->title;
                $_id                = (string) $section->_id;

                $filteredQuestions = (array) array_filter($this->questions, function ($obj) use ($_id) {
                    $question = (object) $obj;
                    return $question->_postId == $_id;
                });

                $pallete_q          = "";
                $total_q            = 0;
                $total_q_pallete    = '';

                if(count($filteredQuestions) > 0) {
                    foreach( $filteredQuestions as $qkey => $question ){
                        $question               = (object) $question;
                        $question_id            = isset($question->_id)? $question->_id : '';
                        $_questionSettings      = isset($question->_questionSettings)? (object) $question->_questionSettings : (object) [];
                        $combine                = isset($_questionSettings->combine)? $_questionSettings->combine : false;
                        $input_type             = isset($_questionSettings->inputType)? (string) $_questionSettings->inputType : null;
                        $question_content       = isset($question->content)? (object) $question->content : (object) [];
                        $_answerSettings        = isset($question->_answerSettings)? (object) $question->_answerSettings : (object) [];
                        $number_of_options      = isset($_answerSettings->numberOfOptions)? (int) $_answerSettings->numberOfOptions : 0;
                        $answerType             = isset($_answerSettings->answerType)? (string) $_answerSettings->answerType : null;
                        $answerTypes            = isset($_answerSettings->answerTypes)? (array) $_answerSettings->answerTypes : (array) [];
                        $questions              = isset($question->questions)? (array) $question->questions : [];
                        $options                = isset($question->options)? (array) $question->options : [];

                        # this varible keep track for each question individual
                        $number_of_questions    = 0;

                        if( $input_type === "fillup" ) {
                            # if its fillup then get regex {}
                            $html = isset($question_content->html)? (string) $question_content->html : "";
                            if(preg_match_all( '/\{([^{}]+)\}/', $html, $multiTextMatches)){
                                $matches                = $multiTextMatches[0];
                                $inner_values           = $multiTextMatches[1];
                                $number_of_questions    += (int) count($matches);
                                
                            }
                            for($i = 0; $i < $number_of_questions; $i++ ){
                                # global question increment
                                $total_q_counter++;

                                # each q - class 
                                $class_qp = '';
                                if( $report != null ) {
                                    $types = isset($report->types)? (array) $report->types : [];
                                    $stats = isset($types[$total_q_counter])? (object) $types[$total_q_counter] : null;
                                    if( $stats != null ) {
                                        if( $stats->status == "correct"){
                                            $class_qp  = '-qp-correct';
                                        } else if( $stats->status == "wrong"){
                                            $class_qp  = '-qp-wrong';
                                        }
                                    }
                                    
                                }

                                # individual section - increment
                                $total_q++;
                                # global pattelte - incrmenet
                                $total_q_pallete .= '<span 
                                    data-start-num="'.($total_q_counter).'"
                                    data-end-num="'.($total_q_counter).'"
                                    class="question-palette__item is-selected '.$class_qp.'" 
                                    data-p="'.($skey + 1).'" 
                                    data-num-c="'.($total_q_counter).'"
                                    data-num="'.$total_q_counter.'"
                                >'.$total_q_counter.'</span>';
                                # review box - increment
                                ob_start(); ?>
                                    <div class="result-table__col" data-p="<?php echo $skey + 1; ?>" data-num="<?php echo $total_q_counter; ?>"><span>Q<?php echo $total_q_counter; ?>:</span><em></em></div>
                                <?php self::$review_boxes .= ob_get_clean();

                                // question_qt
                                self::$question_qt1[] = (object) [
                                    "_id"       => $question_id,
                                    "counter"   => $total_q_counter,
                                    "type"      => $input_type,
                                ];

                                
                            }
                        } else if( $input_type === "select" ) {
                            # if its select then get regex {}
                            $html = isset($question_content->html)? (string) $question_content->html : "";
                            if(preg_match_all( '/\{([^{}]+)\}/', $html, $multiTextMatches)){
                                $matches                = $multiTextMatches[0];
                                $inner_values           = $multiTextMatches[1];
                                foreach( $inner_values as $ikey => $value ) {
                                    // A - $value
                                    if(isset($answerTypes[$answerType])){
                                        if(in_array($value, (array) $answerTypes[$answerType] )) {
                                            $number_of_questions++;
                                        }
                                    }
                                }

                                
                            }
                            for($i = 0; $i < $number_of_questions; $i++ ){
                                # global question increment
                                $total_q_counter++;

                                # each q - class 
                                $class_qp = '';
                                if( $report != null ) {
                                    $types = isset($report->types)? (array) $report->types : [];
                                    $stats = isset($types[$total_q_counter])? (object) $types[$total_q_counter] : null;
                                    if( $stats != null ) {
                                        if( $stats->status == "correct"){
                                            $class_qp  = '-qp-correct';
                                        } else if( $stats->status == "wrong"){
                                            $class_qp  = '-qp-wrong';
                                        }
                                    }
                                    
                                }


                                # individual section - increment
                                $total_q++;
                                # global pattelte - incrmenet
                                $total_q_pallete .= '<span 
                                    data-start-num="'.($total_q_counter).'"
                                    data-end-num="'.($total_q_counter).'"
                                    class="question-palette__item is-selected '.$class_qp.'" 
                                    data-p="'.($skey + 1).'" 
                                    data-num-c="'.($total_q_counter).'"
                                    data-num="'.$total_q_counter.'"
                                >'.$total_q_counter.'</span>';
                                # review box - increment
                                ob_start(); ?>
                                    <div class="result-table__col" data-p="<?php echo $skey + 1; ?>" data-num="<?php echo $total_q_counter; ?>"><span>Q<?php echo $total_q_counter; ?>:</span><em></em></div>
                                <?php self::$review_boxes .= ob_get_clean();

                                // question_qt
                                self::$question_qt1[] = (object) [
                                    "_id"       => $question_id,
                                    "counter"   => $total_q_counter,
                                    "type"      => $input_type,
                                ];
                            }
                        } else if( $input_type === "radio" ) {
                            $number_of_questions += count($questions);
                            for($i = 0; $i < $number_of_questions; $i++ ){
                                # global question increment
                                $total_q_counter++;

                                # each q - class 
                                $class_qp = '';
                                if( $report != null ) {
                                    $types = isset($report->types)? (array) $report->types : [];
                                    $stats = isset($types[$total_q_counter])? (object) $types[$total_q_counter] : null;
                                    if( $stats != null ) {
                                        if( $stats->status == "correct"){
                                            $class_qp  = '-qp-correct';
                                        } else if( $stats->status == "wrong"){
                                            $class_qp  = '-qp-wrong';
                                        }
                                    }
                                    
                                }

                                if(!$this->is_result){
                                    $class_qp = "";
                                }
                                # individual section - increment
                                $total_q++;
                                # global pattelte - incrmenet
                                $total_q_pallete .= '<span 
                                    data-start-num="'.($total_q_counter).'"
                                    data-end-num="'.($total_q_counter).'"
                                    class="question-palette__item is-selected '.$class_qp.'" 
                                    data-num-c="'.($total_q_counter).'"
                                    data-p="'.($skey + 1).'" 
                                    data-num="'.$total_q_counter.'"
                                >'.$total_q_counter.'</span>';
                                # review box - increment
                                ob_start(); ?>
                                    <div class="result-table__col" data-p="<?php echo $skey + 1; ?>" data-num="<?php echo $total_q_counter; ?>"><span>Q<?php echo $total_q_counter; ?>:</span><em></em></div>
                                <?php self::$review_boxes .= ob_get_clean();

                                // question_qt
                                self::$question_qt1[] = (object) [
                                    "_id"       => $question_id,
                                    "counter"   => $total_q_counter,
                                    "type"      => $input_type,
                                ];
                            }
                        } else if( $input_type === "checkbox" ) {
                            
                            if(count($options) > 0) {
                                $correctObjects = array_filter($options, function ($object) {
                                    $option = (object) $object;
                                    return isset($option->correct) && $option->correct == "true";
                                });
                                
                                
                                // Count the filtered array
                                if($combine == "true" || $combine == "1"){
                                    
                                    $number_of_questions += count($correctObjects);
                                } else {
                                    if(count($correctObjects) > 0){
                                        $number_of_questions = 1;
                                    } else {
                                        $number_of_questions = 0;
                                    }
                                }

                                $last_q_counter = $number_of_questions + $total_q_counter;


                                # each q - class 
                                $class_qp = '';
                                if( $report != null ) {
                                    $types = isset($report->types)? (array) $report->types : [];
                                    if( $combine == "true" || $combine == "1" ){
                                        $stats = isset($types[($total_q_counter+1)."-".($last_q_counter)])? (object) $types[($total_q_counter+1)."-".($last_q_counter)] : null;
                                    } else {
                                        $stats = isset($types[($total_q_counter+1)])? (object) $types[($total_q_counter+1)] : null;
                                    }
                                    if( $stats != null ) {
                                        if( $stats->status == "correct"){
                                            $class_qp  = '-qp-correct';
                                        } else if( $stats->status == "wrong"){
                                            $class_qp  = '-qp-wrong';
                                        }
                                    }
                                    
                                }

                                if(!$this->is_result){
                                    $class_qp = "";
                                }
                                # global pattelte - incrmenet
                                if( $combine == "true" || $combine == "1" ){
                                    $total_q_pallete .= '<span 
                                        data-start-num="'.($total_q_counter+1).'"
                                        data-end-num="'.($last_q_counter).'"
                                        data-num-c="'.($total_q_counter + $number_of_questions).'"
                                        class="question-palette__item -group '.$class_qp.'" 
                                        data-p="'.($skey + 1).'" 
                                        data-num="'.($total_q_counter+1).'-'.$last_q_counter.'"
                                    >'.($total_q_counter+1).'<em></em>'.($last_q_counter).'</span>';

                                    # review box - increment
                                    ob_start(); ?>
                                        <div class="result-table__col" data-p="<?php echo $skey + 1; ?>" data-num="<?php echo $total_q_counter+1; ?>-<?php echo $last_q_counter; ?>">
                                        <span>Q<?php echo $total_q_counter+1; ?>-<?php echo $last_q_counter; ?>:</span>
                                        <em></em>
                                    </div>
                                    <?php self::$review_boxes .= ob_get_clean();


                                    // question_qt
                                    self::$question_qt1[] = (object) [
                                        "_id"       => $question_id,
                                        "counter"   => ($total_q_counter+1)."-".$last_q_counter,
                                        "type"      => $input_type,
                                    ];

                                    # global question increment
                                    $total_q_counter += $number_of_questions;
                                    # individual section - increment
                                    $total_q += $number_of_questions;



                                } else {
                                    $total_q_pallete .= '<span 
                                        data-start-num="'.($total_q_counter + 1).'"
                                        data-end-num="'.($total_q_counter + 1).'"
                                        class="question-palette__item is-selected '.$class_qp.'" 
                                        data-num-c="'.($total_q_counter + 1).'"
                                        data-p="'.($skey + 1).'" 
                                        data-num="'.($total_q_counter + 1).'"
                                    >'.($total_q_counter + 1).'</span>';

                                    # review box - increment
                                    ob_start(); ?>
                                        <div class="result-table__col" data-p="<?php echo $skey + 1; ?>" data-num="<?php echo $total_q_counter+1; ?>">
                                        <span>Q<?php echo $total_q_counter+1; ?>:</span>
                                        <em></em>
                                    </div>
                                    <?php self::$review_boxes .= ob_get_clean();

                                    // question_qt
                                    self::$question_qt1[] = (object) [
                                        "_id"       => $question_id,
                                        "counter"   => ($total_q_counter+1),
                                        "type"      => $input_type,
                                    ];
                                    # global question increment
                                    $total_q_counter += 1;
                                    # individual section - increment
                                    $total_q += 1;
                                }
                                
                                
                            }
                        }

                    }
                }

                # this block is for each section - and its realted questions
                ob_start(); ?>
                <div id="navigation-bar-<?php echo $skey+1; ?>" class="question-palette__part <?php echo ($skey == 0)? '-active' : ''; ?>" data-part="<?php echo $skey+1; ?>" data-questions="<?php echo $total_q; ?>">
                    <div class="question-palette__part-title">
                        Part <?php echo $skey+1; ?> <span>:</span>
                    </div>
                    <div class="question-palette__part-status">
                        <span class="number">0</span> 
                        of 
                        <span class="total"><?php echo $total_q; ?></span> 
                        questions
                    </div>
                    <div class="question-palette__items-group">
                    <?php 
                        echo $total_q_pallete; 
                    /*
                        for( $i = 0; $i < $total_q; $i++ ) { 
                            $class_qp = '';

                            if( $report != null ) {
                                $types = isset($report->types)? (array) $report->types : [];
                                $stats = isset($types[$total_q_counter+1])? (object) $types[$total_q_counter+1] : null;
                                if( $stats != null ) {
                                    if( $stats->status == "correct"){
                                        $class_qp  = '-qp-correct';
                                    } else if( $stats->status == "wrong"){
                                        $class_qp  = '-qp-wrong';
                                    }
                                }
                                
                            }
                            // for review boxes
                            ob_start(); ?>
                                <div class="result-table__col" data-p="<?php echo $skey + 1; ?>" data-num="<?php echo $total_q_counter+1; ?>"><span>Q<?php echo $total_q_counter+1; ?>:</span><em></em></div>
                            <?php self::$review_boxes .= ob_get_clean();

                            // for pallete
                            ?>
                            <span 
                                class="question-palette__item <?php echo ($i == 0)? 'is-selected' : ''; ?> <?php echo $class_qp; ?>" 
                                data-p="<?php echo $skey+1; ?>"
                                data-num="<?php echo $total_q_counter+1; ?>"
                            >
                                <?php echo $total_q_counter+1; ?>
                            </span>
                            <?php 
                            $total_q_counter++;
                        } 
                    */


                    ?>
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
    public function get_reading_styles(){
        $site_url = site_url();
        ob_start(); ?>
        <?php if( $this->result ){ ?>
        <style>

            .ielts-lms-result-container {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                margin-right: auto;
                margin-left: auto;
                position: relative;
                max-width: 1140px;
                min-height: 50vh;
                -webkit-box-align: center;
                -ms-flex-align: center;
                align-items: center;
            }

            .ielts-lms-result-col {
                position: relative;
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
            }

            .ielts-lms-result-col {
                min-height: 1px;
            }

            .ielts-lms-result-widget {
                box-shadow: 0px 0px 40px 0px rgb(0 0 0 / 8%);
                margin: 5px 40px 5px 5px;
                --e-column-margin-right: 40px;
                --e-column-margin-left: 5px;
                padding: 5% 5% 5% 5%;
                border-radius: 10px 10px 10px 10px;
                display: flex;
                position: relative;
                width: 100%;
                -ms-flex-wrap: wrap;
                flex-wrap: wrap;
                -ms-flex-line-pack: start;
                align-content: flex-start;
            }

            .ielts-lms-result-widget-banner {
                -webkit-box-orient: vertical;
                -webkit-box-direction: normal;
                -ms-flex-direction: initial;
                flex-direction: initial;
                -ms-flex-wrap: initial;
                flex-wrap: initial;
                -webkit-box-pack: initial;
                -ms-flex-pack: initial;
                justify-content: initial;
                -webkit-box-align: initial;
                -ms-flex-align: initial;
                align-items: initial;
                -ms-flex-line-pack: initial;
                align-content: initial;
                gap: initial;
            }

            .ielts-lms-result-widget-banner:not(:last-child) {
                margin-bottom: 20px;
            }

            .ielts-lms-result-widget-banner {
                width: 100%;
            }

            .ielts-lms-result-widget-banner {
                position: relative;
            }

            .ielts-lms-result-widget-banner img {
                max-width: 58%;
            }

            .ielts-lms-result-widget-banner img {
                height: auto;
                border: none;
                border-radius: 0;
                -webkit-box-shadow: none;
                box-shadow: none;
            }

            .ielts-lms-result-widget-banner img {
                vertical-align: middle;
                display: inline-block;
            }

            @media (max-width: 767px) {
                .ielts-lms-result-section {
                    padding: 10px 10px 10px 10px;
                }

                .ielts-lms-result-progress-title-h2 {
                    font-size: 17px;
                }
            }

            @media (min-width: 768px) {
                .ielts-lms-result-col-left {
                    width: 40%;
                }

                .ielts-lms-result-col-right {
                    width: 60%;
                }

                .ielts-lms-widget-icon-box {
                    -webkit-box-align: start;
                    -ms-flex-align: start;
                    align-items: flex-start;
                }
            }

            @media (min-width: 768px) {
                .ielts-lms-result-col-left {
                    width: 40%;
                }

                .ielts-lms-result-col-right {
                    width: 60%;
                }
            }

            @media screen and (max-width: 800px) {
                .ielts-lms-result-container {
                    padding-left: 10px;
                    padding-right: 10px;
                }

                .ielts-lms-result-widget {
                    margin: 0 0px;
                    max-width: 100%;
                }
            }

            .ielts-lms-result-progress-title-h2 {
                font-size: 19px;
            }

            .ielts-lms-widget {
                --icon-box-icon-margin: 10px;
                width: 33%;
                max-width: 33%;
                position: relative;
            }

            .ielts-lms-widget {
                -webkit-box-orient: vertical;
                -webkit-box-direction: normal;
                -ms-flex-direction: initial;
                flex-direction: initial;
                -ms-flex-wrap: initial;
                flex-wrap: initial;
                -webkit-box-pack: initial;
                -ms-flex-pack: initial;
                justify-content: initial;
                -webkit-box-align: initial;
                -ms-flex-align: initial;
                align-items: initial;
                -ms-flex-line-pack: initial;
                align-content: initial;
                gap: initial;
            }

            .ielts-lms-widget::not(:last-child) {
                margin-bottom: 0;
            }

            .ielts-lms-widget-container {
                margin: 20px 20px 20px 20px;
                padding: 20px 20px 20px 20px;
                border-radius: 10px 10px 10px 10px;
                box-shadow: 0px 0px 10px 0px rgb(0 0 0 / 8%);
            }

            .ielts-lms-widget-area {
                display: flex;
                position: relative;
                width: 100%;
                -ms-flex-wrap: wrap;
                flex-wrap: wrap;
                -ms-flex-line-pack: start;
                align-content: flex-start;
                padding: 10px;
            }

            .ielts-lms-widget-icon-box {
                display: block;
                text-align: center;
            }

            .ielts-lms-widget-icon {
                margin-bottom: 10px;
                margin-right: auto;
                margin-left: auto;
            }

            .ielts-lms-widget-icon-animation {
                font-size: 25px;
                border-radius: 50%;
                padding: 10px;
                width: 55px;
                height: 55px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                flex-wrap: nowrap;
                align-content: center;
            }

            .ielts-lms-widget-icon-animation-1 {
                background-color: #FFF7D4;
                fill: #FEB352;
                color: #FEB352;
            }

            .ielts-lms-widget-icon-animation-2 {
                background-color: #E6E5FF;
                fill: #7672FD;
                color: #7672FD;
            }

            .ielts-lms-widget-icon-animation-3 {
                background-color: #FFE5EA;
                fill: #FF4E5A;
                color: #FF4E5A;
            }

            .ielts-lms-widget-icon {
                display: inline-block;
                line-height: 1;
                -webkit-transition: all .3s;
                -o-transition: all .3s;
                transition: all .3s;
                color: #818a91;
                font-size: 50px;
                text-align: center;
            }

            .ielts-lms-widget-icon-content {
                -webkit-box-flex: 1;
                -ms-flex-positive: 1;
                flex-grow: 1;
            }

            .ielts-lms-widget-icon-content-title {
                font-size: 24px;
                font-weight: 600;
                margin-bottom: 4px;
            }

            .ielts-lms-widget-icon-content-subtitle {
                font-size: 14px;
            }

            .ielts-lms-widget-4 {
                width: 100%;
                max-width: 100%;
            }

            .ielts-lms-widget-4 .ielts-lms-widget-container {
                margin: 0;
                padding: 0;
            }

            .ielts-lms-result-show-button {
                fill: #6C63FF;
                color: #6C63FF;
                background-color: #FFFFFF00;
                border-style: solid;
                border-width: 2px 2px 2px 2px;
                border-color: #6C63FF;
                border-radius: 5px 5px 5px 5px;
                font-size: 18px;
                padding: 20px 40px;
                width: 100%;
            }

            @media (max-width: 1000px) {
                body {}

                .p-0-at-1000px {
                    padding: 0 !important;
                }

                .m-0-at-1000px {
                    margin: 0 !important;
                }

                .mb-50-at-1000px {
                    margin-bottom: 50px !important;
                }

                .ielts-lms-result-container {
                    display: flex;
                    flex-direction: column;
                }

                .ielts-lms-result-col-left {
                    width: 70%;
                    margin-top: 40px;
                }

                .ielts-lms-result-col-right {
                    width: 100%;
                    max-width: 100%;
                }
            }

            @media (max-width: 580px) {
                .ielts-lms-widget-area>div {
                    width: 100% !important;
                    max-width: 100%;
                }
            }


            .proqyz__explain {
                background-color: #f9a95a;
                border-radius: 2px;
                padding: 0px;
            }

            .proqyz__explainqe:before {
                content: "Example";
            }
            .proqyz__explain:before {
                font-style: normal;
                font-weight: 700;
                padding: 0px 5px;
                background-color: #f78f29;
            }

            .proqyz__explain:before {
                content: attr(data-no);
            }

            .iot-select button.dropdown-toggle {
                height: 40px;
                line-height: 40px;
                border-radius: 4px;
                background-color: #fff !important;
                color: #9d9d9d;
                outline: none !important;
                border: 1px solid #cccccc !important;
                font-weight: normal;
                font-size: 14px;
                letter-spacing: 0.2px;
                padding: 0 12px;
                box-shadow: none;
                max-width: 150px;
                border-radius: 50px;
            }

            [data-type="question-report"] {
                cursor: pointer;
            }

        </style>    
        <?php } ?>
        <style>
            .--no-controls .plyr__controls {
                justify-content: center !important;
            }
            .realtest-header__time.d-none {
                display: none;
            }
        </style>
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

            span.qp-item {
                background: var(--reading-color);
                color: white;
                display: inline-flex;
                width: 24px;
                height: 24px;
                align-items: center;
                justify-content: center;
                border-radius: 14px;
                font-size: 12px;
            }

            .test-panel__question-desc p {
                margin: 0;
            }

            .test-panel__answers-wrap {
                margin: 0 !important;
            }

            .test-panel__answer .test-panel__answer-item {
                padding-left: 15px;
            }

            

            .reading-test-result .answer .b-r {
                font-weight: bold;
                color: red;
            }

            .reading-test-result .sl-item.explanation {
                margin: 0px 0px 5px 0px !important;
            }

            .reading-test-result .sl-item.explanation .sl-control {
                top: 0;
            }
            .reading-test-result .sl-item .sl-control {
                display: inline-block;
                margin: 5px 0px 0px 10px;
                position: relative;
                top: 8px;
            }

            .reading-test-result .sl-item .sl-control a {
                display: inline-block;
                line-height: 25px;
                min-height: 25px;
                padding: 0px 10px;
                margin: 0px 0px 5px 5px;
                border-radius: 3px;
                -moz-transition: all ease 0.2s;
                -o-transition: all ease 0.2s;
                -webkit-transition: all ease 0.2s;
                transition: all ease 0.2s;
                font-size: 10px;
                color: #327846;
                border: 1px solid #327846;
                font-family: "Montserrat",Helvetica,Arial,sans-serif;
                white-space: nowrap;
            }

            .reading-test-result .sl-control a span.icon-explain {
                background-image: url(<?php echo $site_url; ?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/img/ielts/reading/icon_explain.png);
            }

            .reading-test-result .sl-item .sl-control a span {
                background-repeat: no-repeat;
                background-position: center center;
                width: 16px;
                height: 17px;
                display: inline-block;
                position: relative;
                margin: 0px 3px 0px 0px;
                top: -1px;
                vertical-align: middle;
                background-size: contain;
            }
            
            button:disabled {
                opacity: 0.6;
                cursor: no-drop;
            }


            
        </style>

        <?php echo ob_get_clean();
    }

    /**
     * changes @1.2.1.2
     */
    public function header(){
        if( $this->solution ){ } else if( $this->result ) {
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
                <link rel="stylesheet" href="<?php echo site_url() . '/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/css/proqyz-quiz.css'; ?>" />
                <?php echo $this->get_reading_styles(); ?>
                <script>
                    const resultUrl     = `${window.location.href}`;
                    const resultId      = `<?php echo $this->result_id; ?>`;
                    const quizId        = `<?php echo $this->_id; ?>`;
                    const quizCategory  = `<?php echo $this->category; ?>`;
                </script>
                <?php wp_head(); ?>                
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
                <link rel="stylesheet" href="<?php echo site_url() . '/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/css/proqyz-quiz.css'; ?>" />
                <?php echo $this->get_reading_styles(); ?>
                <script>
                    
                    const quizId        = `<?php echo $this->_id; ?>`;
                    const quizCategory  = `<?php echo $this->category; ?>`;
                    const fullMockTest  = `<?php echo $this->is_fullmock_test? 'true' : 'false'; ?>`;
                </script>
                <?php wp_head(); ?>
            </head>
            <?php return ob_get_clean();
        }

    }

    public function footer(){

        if( $this->solution ){ } else {
            ob_start(); ?>
            <script id="session-details" type="application/json">
                <?php 
                    echo json_encode( (object) [
                        "_id"           => $this->_id,
                        "category"      => $this->category,
                        "time"          => $this->default,
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
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>        
            <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js" integrity="sha512-zMfrMAZYAlNClPKjN+JMuslK/B6sPM09BGvrWlW+cymmPmsUT1xJF3P4kxI3lOh9zypakSgWaTpY6vDJY/3Dig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js" integrity="sha512-NhRZzPdzMOMf005Xmd4JonwPftz4Pe99mRVcFeRDcdCtfjv46zPIi/7ZKScbpHD/V0HB1Eb+ZWigMqw94VUVaw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/split.js/1.6.2/split.min.js" integrity="sha512-to2k78YjoNUq8+hnJS8AwFg/nrLRFLdYYalb18SlcsFRXavCOTfBF3lNyplKkLJeB8YjKVTb1FPHGSy9sXfSdg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
            <script src="<?php echo site_url();?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/js/uuidv4.js"></script>
            <script src="<?php echo site_url();?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/js/texthighter.js"></script>
            <script src="<?php echo site_url();?>/wp-content/plugins/spacetree/libs/proqyz/includes/templates/public/quiz/dist/js/reading.js" type="text/javascript"></script>
            <?php wp_footer(); ?>            
            <?php return ob_get_clean();
        }
    }

    /**
     * changes @1.2.1.2
     */
    public function get_layout(){
        if( $this->result ) { } else {
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
            $practice_mode_time_limit_disabled = isset($practice_mode->quizTimer)? $practice_mode->quizTimer : 0;

            if($practice_enable) {
                # true means disabled time limit
                if($practice_mode_time_limit_disabled) {
                    $time_limit = 0;
                }
                
            }

            ob_start(); ?>
            <!DOCTYPE html>
            <html <?php language_attributes(); ?>>
            <?php echo $this->header(); ?>
            <body class="reading-test show-palette take-test-page -practice-mode has-glyphicons <?php echo $class_body; ?>">
                <div class="dialog-off-canvas-main-canvas js-attempt-only-reading">

                    <header class="realtest-header ">
                        <span class="realtest-header__logo practice-item__icon -reading d-none-sm-550px"></span>
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
                            
                            <?php if( !$this->is_fullmock_test ){ ?>
                                <a style="color: #d61c1c;" class="realtest-header__bt-review btn__report-question use-ajax" href="#!"> 
                                    <span class="ioticon-alert-triangle"></span>
                                </a>
                                <!--div class="realtest-header__icon -full-screen" id="js-full-screen" data-original-title="Full Screen Mode" data-placement="bottom" data-trigger="hover">
                                </div-->
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
                                            <?php echo $this->get_questions_html(); ?>
                                            
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
                                        <?php echo $this->get_questions_pallete(); ?>

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
        global $wpdb, 
        $table_proqyz_quiz_progress, 
        $table_proqyz_quiz_progress_meta, 
        $table_proqyz_groups, 
        $table_proqyz_quiz_review, 
        $table_proqyz_quiz_review_meta,
        $ST_OPTION_st_proqyz__leaderboard;

        $enable_leaderboard = get_option($ST_OPTION_st_proqyz__leaderboard, 1);

        $quiz_title         = $this->quiz->title;
        $quiz_settings      = $this->quiz->settings;
        $quiz_scores        = isset($quiz_settings->scores)? (object) $quiz_settings->scores : (object) [
            "score_type"    => "band"
        ];
        $quiz_score_type    = isset($quiz_scores->score_type)? (string) $quiz_scores->score_type : 'band';

        $questions_html     = $this->get_questions_html();
        $report             = (object) $this->get_report();
        $class_body         = '-result';

        $total_questions    = isset($report->total)? (int) $report->total : 0;
        $total_correct      = isset($report->correct)? (int) $report->correct : 0;
        $percentage         = 0;
        
        $remaining_time     = (int) $this->seconds;
        $total_time         = (int) $this->default;
        
        if ($total_questions > 0) {
            $percentage = ($total_correct * 100) / $total_questions;
            $percentage = round($percentage / 5) * 5; // Round to the nearest multiple of 5
        }
        
        if ($total_time > 0) {
            $spent_time = ($remaining_time - $total_time);
            $time_percentage = ($remaining_time) / $total_time;
            $time_percentage = 100 - round(($time_percentage * 100) / 5) * 5; // Round to the nearest multiple of 5
        }



        

        if( $this->result_id != null ) {
            $wpdb->update($table_proqyz_quiz_progress, [
                "score" => json_encode((object) $report)
            ], [ "ID" => $this->result_id ]);
        }

        $avatar_url             = get_avatar_url($this->student_id);
        $student_info           = get_userdata($this->student_id);
        $student_display_name   = $student_info->display_name; // User's display name
        $student_username       = $student_info->user_login;    

        ob_start(); ?>
            <!DOCTYPE html>
            <html <?php language_attributes(); ?>>
            <?php echo $this->header(); ?>
            <body style="background-color:#f5f5f5f7;" class="reading-test reading-result reading-test-result show-palette take-test-page -practice-mode <?php echo $class_body; ?>">
                
            
                <div class="dialog-off-canvas-main-canvas">

                    <header class="realtest-header ">
                        <span class="realtest-header__logo practice-item__icon -reading "></span>
                        <?php if(isset($this->quiz->title)) { ?>
                            <div class=""><?php echo $this->quiz->title; ?></div>
                        <?php } ?>

                        <div class="realtest-header__time d-none" style="display:none;">
                            
                        </div>
                        <div class="realtest-header__btn-group">
                            <a href="/" class="realtest-header__bt-submit -icon-chevron-left -reading--btn" style="background: #f5f5f5;color: #296239;">
                                Home
                            </a>
                            <a href="<?php echo $this->attempt_url; ?>" class="realtest-header__bt-submit -reading--btn">
                                Retake
                            </a>

                        </div>
                    </header>
            
                    <div class="container page" style="margin-top:100px;">

                        
                        <div class="row" id="result-frame">

                            <div class="<?php echo ($enable_leaderboard == 1)? 'col-md-8' : 'col-md-12'; ?> col-sm-12 col-xs-12" style="background: white;padding-top: 30px;padding-bottom: 30px;border-radius: 25px;margin-bottom: 20px;">

                                <div class="user-box" style="margin:10px 0px 0px 0px;">
                                
                                    <div class="us-user">
                                        <img alt="<?php echo $student_display_name; ?>" src="<?php echo $avatar_url; ?>">
                                        <p><?php echo $student_display_name; ?></p>
                                    </div>
                                
                                    <div class="user-socer green">
                                        <h2>Your score is:</h2>
                                        <div class="row">
                                            <div class="col-md-4  col-sm-4 col-xs-12">
                                                <div class="radial progress-<?php echo $percentage; ?> small" id="progress-small">
                                                <div class="overlay">
                                                    <span class="pr-icon-true"></span>
                                                    <p>Correct Answers</p>
                                                    <strong><?php echo $total_correct; ?>/<?php echo $total_questions; ?></strong>
                                                </div>
                                                <div class="dot"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <div class="welldone">
                                                    <div class="wd-round">
                                                        <strong>
                                                            <?php if($quiz_score_type == "band"){ ?>
                                                                <?php echo $this->Ielts_Lms_scoreToAcademic($total_correct); ?>  
                                                            <?php } ?>
                                                            <?php if($quiz_score_type == "percentage"){ ?>
                                                                <?php echo number_format($percentage, 0); ?>
                                                            <?php } ?>
                                                        </strong>
                                                        <em class="ielts-lms-widget-icon-content-subtitle">
                                                            <?php if($quiz_score_type == "band"){ ?>Band<?php } ?>
                                                            <?php if($quiz_score_type == "percentage"){ ?>Percentage<?php } ?>
                                                        </em>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4  col-sm-4  col-xs-12">
                                                <div class="radial progress-<?php echo $time_percentage; ?> big" id="progress-big">
                                                    <div class="overlay">
                                                        <span class="pr-icon-time"></span>
                                                        <p>Time Spent</p>
                                                        <strong><?php echo $this->seconds_to_hms($total_time - $remaining_time); ?> </strong>
                                                        <em>(<?php echo $this->seconds_to_hms($total_time); ?> )</em>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 reviews-test-mobile" style="margin-top: 30px;">
                                                <a href="#!" class="btn-show-re" id="show-result-modal" data-target="#result-modal" type="button" role="button">
                                                    <span></span>  Enter Review & Explanations
                                                </a>
                                                    
                                                <?php if(isset($this->session->session_category)){
                                                    if($this->session->session_category == "course" && $this->session->course_category !== "sfwd-course"){
                                                        $course_id = $this->session->course_id;
                                                        $course_url = st_proqyz_get_course_url( $course_id );
                                                        ?>
                                                        <a href="#!" class="btn-show-re" onclick="window.location.href = '<?php echo $course_url; ?>' " type="button" role="button">
                                                            Back to course
                                                        </a>
                                                    <?php }
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!---box-->
                                </div>




                            </div> 
                            <?php if($enable_leaderboard == 1) { ?><div class="col-md-4 col-sm-12 col-xs-12">
                                <!-- widget tip item -->
                                <?php echo do_shortcode("[proqyz-leaderboard result_id='$this->result_id']"); ?>
                            </div> 
                            <?php } ?>     






                        </div>

                        <div class="row">
                            <div class="col-12">
                                <?php echo do_shortcode("[proqyz-result-page result_id='$this->result_id']"); ?>
                            </div>
                        </div>

                        <div class="mt-10 mb-10">
                            <?php /* <pre><?php print_r($this->get_report()); ?></pre> */ ?>
                        </div>

                    </div>

                </div>


                <div class="modal fade" id="result-modal" tabindex="3" data-backdrop="static" data-keyboard="false" aria-labelledby="result-modalLabel" style="display: none;background:white;" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen" style="display:contents;">
                        <div class="modal-content p-0 m-0" style="display:contents;">
                            <div class="dialog-off-canvas-main-canvas js-attempt-only-reading" style="background:white;">

                                <header class="realtest-header ">
                                    <span class="realtest-header__logo practice-item__icon -reading "></span>
                                    <?php if(isset($this->quiz->title)) { ?>
                                    <div class=""><?php echo $this->quiz->title; ?></div>
                                    <?php } ?>

                                    <div class="realtest-header__time " style="display:none;">
                                        <span class="realtest-header__time-clock" data-current-time="" data-time="<?php echo $this->seconds; ?>" data-duration-default="<?php echo $this->default; ?>" id="time-clock">
                                            <span class="realtest-header__time-val">-:-</span>
                                            <span class="realtest-header__time-text">minutes remaining</span>
                                        </span>
                                    </div>
                                    <div class="realtest-header__btn-group">
                                        <a style="color: #d61c1c;" class="realtest-header__bt-review btn__report-question use-ajax" href="#!"> 
                                            <span class="ioticon-alert-triangle"></span>
                                        </a>
                                        <button class="realtest-header__bt-review" data-target="#modal-review-test">
                                            <span class="ioticon-review"></span>Review
                                        </button>
                                        <button class="realtest-header__bt-review" data-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
                                </header>

                                <div class="page take-test">


                                    <div class="take-test__body">

                                        <div class="region region-content">
                                            <article role="article">
                                                <div class="take-test__board highlighter-context" id="highlighter-contents">
                                                    <div id="split-one" class="take-test__split-item">
                                                        <?php echo $this->get_sections_html(); ?>
                                                    </div>
                                                    <div id="split-two" class="take-test__split-item">
                                                        <?php echo $questions_html; ?>
                                                        
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
                                            </article>
                                        </div>
                                        <div class="take-test__bottom-palette">
                                            <div class="question-palette">
                                                <div class="question-palette__list-item" id="question-palette-table">
                                                    <?php echo $this->get_questions_pallete(); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::MODALS-->
                <?php echo $this->modals(); ?>
                <!--end::MODALS-->
                

                <?php echo $this->footer(); ?>
            </body>
            </html>
        <?php return ob_get_clean(); ?>




        <!--div class="col-md-12">
                            <div class="ielts-lms-result-container p-0-at-1000px m-0-at-1000px mb-50-at-1000px">
                                
                                <div class="ielts-lms-result-col-right ielts-lms-result-col">
                                    <div class="ielts-lms-widget-area">
                                        
                                        <div class="ielts-lms-widget ielts-lms-widget-1">
                                            <div class="ielts-lms-widget-container">
                                                <div class="ielts-lms-widget-icon-box">
                                                    <div class="ielts-lms-widget-icon">
                                                        <div class="ielts-lms-widget-icon-animation ielts-lms-widget-icon-animation-1">
                                                            <i aria-hidden="true" class="fas fa-star"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ielts-lms-widget-icon-content">
                                                        <h3 class="ielts-lms-widget-icon-content-title">
                                                        <?php echo $total_correct; ?>/<?php echo $total_questions; ?>                                    
                                                        </h3>
                                                        <p class="ielts-lms-widget-icon-content-subtitle">Correct Answers</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="ielts-lms-widget ielts-lms-widget-2">
                                            <div class="ielts-lms-widget-container">
                                                <div class="ielts-lms-widget-icon-box">
                                                    <div class="ielts-lms-widget-icon">
                                                        <div class="ielts-lms-widget-icon-animation ielts-lms-widget-icon-animation-2">
                                                            <i aria-hidden="true" class="fas fa-user-graduate"></i>
                                                        </div>
                                                        </div>
                                                        <div class="ielts-lms-widget-icon-content">
                                                        <h3 class="ielts-lms-widget-icon-content-title">
                                                            <?php if($quiz_score_type == "band"){ ?>
                                                                <?php echo $this->Ielts_Lms_scoreToAcademic($total_correct); ?>  
                                                            <?php } ?>
                                                            <?php if($quiz_score_type == "percentage"){ ?>
                                                                <?php echo number_format($percentage, 2)."%"; ?>
                                                            <?php } ?>
                                                        </h3>
                                                        <p class="ielts-lms-widget-icon-content-subtitle">
                                                            <?php if($quiz_score_type == "band"){ ?>Band<?php } ?>
                                                            <?php if($quiz_score_type == "percentage"){ ?>Percentage<?php } ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="ielts-lms-widget ielts-lms-widget-3">
                                            <div class="ielts-lms-widget-container">
                                                <div class="ielts-lms-widget-icon-box">
                                                    <div class="ielts-lms-widget-icon">
                                                    <div class="ielts-lms-widget-icon-animation ielts-lms-widget-icon-animation-3">
                                                        <i aria-hidden="true" class="fas fa-stopwatch"></i>
                                                    </div>
                                                    </div>
                                                    <div class="ielts-lms-widget-icon-content">
                                                    <h3 class="ielts-lms-widget-icon-content-title">
                                                        <?php echo $this->seconds_to_hms($total_time - $remaining_time); ?>                          
                                                    </h3>
                                                    <p class="ielts-lms-widget-icon-content-subtitle">Time Spent</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="ielts-lms-widget ielts-lms-widget-4">
                                            <div class="ielts-lms-widget-container">
                                                <button style="margin-bottom:1rem;" class="ielts-lms-result-show-button" id="show-result-modal" data-target="#result-modal" type="button" role="button">Scores And Explanation</button>
                                                
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
                                        </div>
                                        
                                    </div>
                                </div>
                                

                                
                                <div class="ielts-lms-result-col-left ielts-lms-result-col">
                                    <div class="ielts-lms-result-widget">
                                        
                                    </div>
                                </div>
                                
                            </div>
                        </div-->                                















    <?php }

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
                <?php echo self::get_report_questions_modal(); ?>
                <div class="modal fade modal-review-test" id="modal-review-test" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-keyboard="false" data-backdrop="static" style="display:none;background:#0000007a;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <i class="ioticon-x modal-review-test__close-modal" data-dismiss="modal" aria-label="Close"></i>
                            <div class="modal-body">
                                <h4 class="modal-review-test__title">Review your answers</h4>
                                <p class="modal-review-test__caption">* This window is to review your answers only, you cannot change the answers in here</p>
                                <div class="modal-review-test__table">
                                    <div class="result-table" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:center;">
                                        <?php echo self::$review_boxes; ?> 
                                    </div>
                                </div>
                                <div class="modal-review-test__footer">
                                    <button type="button" class="modal-review-test__btn iot-grbt -main-color" data-dismiss="modal">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php return ob_get_clean();
        } else {
            ob_start(); ?>
                <?php echo self::get_report_questions_modal(); ?>
                <div class="modal fade modal-submit-test" id="modal-submit-test" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="display:none;" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <i class="ioticon-x modal-submit-test__close-modal" data-dismiss="modal" aria-label="Close"></i>
                            <div class="modal-body">
                                <div class="modal-submit-test__icon"></div>
                                <h4 class="modal-submit-test__title">Are you sure you want to submit?</h4>
                                <div class="modal-submit-test__footer">
                                    <button type="button" class="modal-submit-test__btn iot-grbt -white proqyz_btn" data-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button id="modal-submit-test__btn" data-id="<?php echo $this->_id; ?>" data-category="<?php echo $this->category; ?>" type="button" class="proqyz_btn modal-submit-test__btn iot-grbt -main-color -btn-submit-test">
                                        Submit and Review Answers
                                    </button>
                                </div>
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


                <?php if( !$this->is_fullmock_test ){ ?> <div class="modal fade modal-time-up" id="modal-finish" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-keyboard="false" data-backdrop="static" style="display:none;">
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
                                <?php if( !$this->is_fullmock_test ){ ?>
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
                                <?php if( !$this->is_fullmock_test ){ ?>
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

                <div class="modal fade modal-review-test" id="modal-review-test" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-keyboard="false" data-backdrop="static" style="display:none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <i class="ioticon-x modal-review-test__close-modal" data-dismiss="modal" aria-label="Close"></i>
                            <div class="modal-body">
                                <h4 class="modal-review-test__title">Review your answers</h4>
                                <p class="modal-review-test__caption">* This window is to review your answers only, you cannot change the answers in here</p>
                                <div class="modal-review-test__table">
                                    <div class="result-table" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:center;">
                                        <?php echo self::$review_boxes; ?> 
                                    </div>
                                </div>
                                <div class="modal-review-test__footer">
                                    <button type="button" class="modal-review-test__btn iot-grbt -main-color" data-dismiss="modal">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade modal-view-solution" id="modal-resume" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="display: none;" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content"> 
                            
                            <div class="modal-body">
                                <div class="modal-view-solution__icon"></div>
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


            <?php return ob_get_clean();
        }
    }

    public function Ielts_Lms_scoreToAcademic($global_score){
        $b_pre = 0;
        if($global_score >= 0 && $global_score <= 3 ){
            return 2;
            $b_pre = "20";
        }else if($global_score >= 4 && $global_score <= 5 ){
            return 2.5;
            $b_pre = "25";
        }else if($global_score >= 6 && $global_score <= 7 ){
            return 3;
            $b_pre = "30";
        }else if($global_score >= 8 && $global_score <= 9 ){
            return 3.5;
            $b_pre = "40";
        }else if($global_score >= 10 && $global_score <= 12 ){
            return 4;
            $b_pre = "45";
        }else if($global_score >= 13 && $global_score <= 15 ){
            return 4.5;
            $b_pre = "50";
        }else if($global_score >= 16 && $global_score <= 17 ){
            return 5;
            $b_pre = "55";
        }else if($global_score >= 18 && $global_score <= 22 ){
            return 5.5;
            $b_pre = "60";
        }else if($global_score >= 23 && $global_score <= 25 ){
            return 6;
            $b_pre = "65";
        }else if($global_score >= 26 && $global_score <= 29 ){
            return 6.5;
            $b_pre = "70";
        }else if($global_score >= 30 && $global_score <= 31 ){
            return 7;
            $b_pre = "75";
        }else if($global_score >= 32 && $global_score <= 34 ){
            return 7.5;
            $b_pre = "80";
        }else if($global_score >= 35 && $global_score <= 36 ){
            return 8;
            $b_pre = "90";
        }else if($global_score >= 37 && $global_score <= 38 ){
            return 8.5;
            $b_pre = "95";
        }else if($global_score >= 39 && $global_score <= 40 ){
            return 9;
            $b_pre = "100";
        }else if($global_score > 40 ){
            return 9;
            $b_pre = "100";
        }else if($global_score <= 0){
            $b_pre = "0";
            return 0;
        }else{
            $b_pre = "0";
            return 0;
        }
        return $b_pre;
    }

    public static function Ielts_Lms_scoreToBand($global_score){
        $b_pre = 0;
        if($global_score >= 0 && $global_score <= 3 ){
            return 2;
            $b_pre = "20";
        }else if($global_score >= 4 && $global_score <= 5 ){
            return 2.5;
            $b_pre = "25";
        }else if($global_score >= 6 && $global_score <= 7 ){
            return 3;
            $b_pre = "30";
        }else if($global_score >= 8 && $global_score <= 9 ){
            return 3.5;
            $b_pre = "40";
        }else if($global_score >= 10 && $global_score <= 12 ){
            return 4;
            $b_pre = "45";
        }else if($global_score >= 13 && $global_score <= 15 ){
            return 4.5;
            $b_pre = "50";
        }else if($global_score >= 16 && $global_score <= 17 ){
            return 5;
            $b_pre = "55";
        }else if($global_score >= 18 && $global_score <= 22 ){
            return 5.5;
            $b_pre = "60";
        }else if($global_score >= 23 && $global_score <= 25 ){
            return 6;
            $b_pre = "65";
        }else if($global_score >= 26 && $global_score <= 29 ){
            return 6.5;
            $b_pre = "70";
        }else if($global_score >= 30 && $global_score <= 31 ){
            return 7;
            $b_pre = "75";
        }else if($global_score >= 32 && $global_score <= 34 ){
            return 7.5;
            $b_pre = "80";
        }else if($global_score >= 35 && $global_score <= 36 ){
            return 8;
            $b_pre = "90";
        }else if($global_score >= 37 && $global_score <= 38 ){
            return 8.5;
            $b_pre = "95";
        }else if($global_score >= 39 && $global_score <= 40 ){
            return 9;
            $b_pre = "100";
        }else if($global_score > 40 ){
            return 9;
            $b_pre = "100";
        }else if($global_score <= 0){
            $b_pre = "0";
            return 2;
        }else{
            $b_pre = 2;
            return 2;
        }
        return $b_pre;
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
    
}



