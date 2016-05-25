<?php

namespace Anax\Questions;
use DateTime;

/**
 * Model for Users.
 *
 */
class Question extends \Anax\Users\CDatabaseModel {


    public function findQuestion($question){
        $this->db->select()
            ->from($this->getSource())
            ->where("question_id = ?");

        $this->db->execute([$question]);

        return $this->db->fetchAll($this);

        //$params = array($user, $password);
        //$res = $this->ExecuteSelectQueryAndFetchAll("SELECT * FROM user WHERE acronym = ? AND password = md5(concat(?, salt));", $params);

    }

    public function getSource()
    {
        return strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
    }

    public function getProperties()
    {
        $properties = get_object_vars($this);
        unset($properties['di']);
        unset($properties['db']);

        return $properties;
    }

    public function getAllTags(){
        $this->db->select()->from('tags');

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function getMostPopularTags(){
        $this->db->select()->from('tags')->orderBy('count DESC')->limit('5');

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function getQuestionsByTag($id){

        $this->db->select()->from($this->getSource())
            ->join('question_tags', 'question.question_id = question_tags.question_id')
            ->join('user', 'question.user_id = user.id')
            ->where('tag_id = ?')
            ->orderBy('made ASC');

        $this->db->execute([$id]);
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function getAnswersForQuestion($id){
        $this->db->select()
            ->from('comments')
            ->where("question_id = ?")
            ->join('user', 'comments.user_id = user.id')
            ->andWhere('comment_parent_id IS NULL')
            ->orderBy('time_created ASC');

        $this->db->execute([$id]);


        return $this->db->fetchAll($this);
    }

    public function getAnswerByID($id){
        $this->db->select()
            ->from('comments')
            ->join('user', 'comments.user_id = user.id')
            ->where("comment_id = ?");

        $this->db->execute([$id]);


        return $this->db->fetchInto($this);
    }

    public function setProperties($properties)
    {
        // Update object with incoming values, if any
        if (!empty($properties)) {
            foreach ($properties as $key => $val) {
                $this->$key = $val;
            }
        }
    }

    public function findAll()
    {
        $this->db->select()->from($this->getSource())
            ->join('user', 'question.user_id = user.id')
            //->join('question_tags', 'question.question_id = question_tags.question_id')
            ->orderBy('made DESC');

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function findLatestQuestions()
    {
        $this->db->select()->from($this->getSource())
            ->join('user', 'question.user_id = user.id')
            ->limit('5')
            ->orderBy('made DESC');

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function find($id)
    {
        $this->db->select()
            ->from($this->getSource())
            ->join('user', 'question.user_id = user.id')
            ->where("question_id = ?");

        $this->db->execute([$id]);
        return $this->db->fetchInto($this);
    }

    public function findUserQuestions($id){
        $this->db->select()
            ->from('question')
            ->where("user_id = ?");

        $this->db->execute([$id]);


        return $this->db->fetchAll($this);
    }

    public function findComments($id){
        $this->db->select()
            ->from('comments')
            ->where("comment_id = ?");

        $this->db->execute([$id]);

        return $this->db->fetchAll($this);
    }

    public function findCommentsOfComments($id){
        $this->db->select()
            ->from('comments')
            ->where("comment_id = ?")
            ->andWhere("comment_parent_id = ?");

        $this->db->execute([$id]);

        return $this->db->fetchAll($this);
    }

    public function findAllComments(){
        $this->db->select()
            ->from('comments')
            ->join('user', 'comments.user_id = user.id')
            ->where('comment_parent_id IS NOT NULL');

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function save($values = [])
    {
        $this->setProperties($values);

        $values = $this->getProperties();

        if (isset($values['question_id'])) {
            return $this->updateQuestion($values);
        } else {
            return $this->create($values);
        }
    }

    public function saveAnswer($values = []){
        $userID = $values['user_id'];
        $questionID = $values['question_id'];
        $keys   = array_keys($values);
        $values = array_values($values);

        $this->db->insert(
            'comments',
            $keys
        );
        $res = $this->db->execute($values);

        if($res){
            $this->updateScore($userID);
            $this->updateAnswerCountUser($userID);
            $this->updateAnswerCountQuestion($questionID);
        }

        $this->id = $this->db->lastInsertId();

        return $res;
    }

    public function saveComment($values = []){
        $userID = $values['user_id'];
        $keys   = array_keys($values);
        $values = array_values($values);

        $this->db->insert(
            'comments',
            $keys
        );
        $res = $this->db->execute($values);

        if($res){
            $this->updateScore($userID);
            $this->updateAnswerCountUser($userID);
        }

        $this->id = $this->db->lastInsertId();

        return $res;
    }

    public function create($values)
    {
        $tags = $values['tags'];
        unset($values['tags']);

        $this->addTags($tags);

        $userID = $values['user_id'];
        $keys   = array_keys($values);
        $values = array_values($values);
        $this->db->insert(
            $this->getSource(),
            $keys
        );
        $res = $this->db->execute($values);

        if($res){
            $this->updateScore($userID);
            $this->updateQuestionCountUser($userID);
            $this->updateUserActivity($userID);
        }

        $this->id = $this->db->lastInsertId();

        $this->bindTagToQuestion($this->id, $tags);

        return $res;
    }

    public function findAllTags(){
        $tagsArray = [];
        $res = $this->findAll();
        foreach($res as $question){

            $tags = $this->findAllTagsForQuestion($question->question_id);
            $tagNames = [];
            foreach($tags as $tag){
                $theTag = $this->findByID($tag->tag_id);
                $tagName = $theTag[0]->title;
                array_push($tagNames, $tagName);
            }
            $tagsArray[$question->question_id]= $tagNames;
        }
        return $tagsArray;
    }

    public function findAllTagsForUser($id){
        $tagsArray = [];
        $res = $this->findUserQuestions($id);
        foreach($res as $question){

            $tags = $this->findAllTagsForQuestion($question->question_id);
            $tagNames = [];
            foreach($tags as $tag){
                $theTag = $this->findByID($tag->tag_id);
                $tagName = $theTag[0]->title;

                array_push($tagNames, $tagName);
            }
            $tagsArray[$question->question_id]= $tagNames;
        }
        return $tagsArray;
    }

    public function findAllTagsForQuestion($id){

        $this->db->select()
            ->from('question_tags')
            ->where("question_id = ?");

        $this->db->execute([$id]);
        return $this->db->fetchAll($this);
    }

    public function findTag($tag){
        $this->db->select()
            ->from('tags')
            ->where("title = ?");

        $this->db->execute([$tag]);
        return $this->db->fetchInto($this);
    }

    public function findByID($id){
        $this->db->select()
            ->from('tags')
            ->where("tag_id = ?");

        $this->db->execute([$id]);
        return $this->db->fetchAll($this);
    }

    public function addTags($theTags){
        $column = array('title', 'count');
        $tags = explode(",", $theTags);

        foreach($tags as $tag){
            $string = preg_replace('/\s+/', '', $tag);
            $string = str_replace('#', '', $string);

            $res = $this->findTag($string);
            if(!$res){
                if(!empty($string)){
                    $tagAsArray = array($string);
                    $this->db->insert(
                        'tags',
                        $column
                    );

                    $this->db->execute($tagAsArray);
                }
            }

            $res = $this->findTag($string);
            $this->addTagCount($res);
        }
    }

    public function addTagCount($tag){

        $count = $tag->count;

        $values = [$count + 1, $tag->tag_id];
        $keys = ['count'];
        $this->db->update(
            'tags',
            $keys,
            "tag_id = ?"
        );

        return $this->db->execute($values);
    }

    public function bindTagToQuestion($id, $theTags) {

        $tags = explode(",", $theTags);

        $columns = array('tag_id', 'question_id');

        foreach ($tags as $tag) {
            $string = preg_replace('/\s+/', '', $tag);
            $string = str_replace('#', '', $string);
            $res = $this->findTag($string);
            $tagID = $res->tag_id;

            $makeArray = array($tagID, $id);
            $this->db->insert(
                'question_tags',
                $columns
            );
            $this->db->execute($makeArray);
        }
    }

    public function updateQuestion($values)
    {
        $keys   = array_keys($values);
        $values = array_values($values);


        // Its update, remove id and use as where-clause
        unset($keys['id']);
        $values[] = $this->id;


        $this->db->update(
            $this->getSource(),
            $keys,
            "id = ?"
        );

        return $this->db->execute($values);
    }

    public function updateScore($id){

        $this->db->select()
            ->from('user')
            ->where("id = ?");

        $this->db->execute([$id]);

        $res = $this->db->fetchInto($this);

        $currentScore = $res->score;
        $values = [$currentScore + 1, $id];
        $keys = ['score'];
        $this->db->update(
            'user',
            $keys,
            "id = ?"
        );

        return $this->db->execute($values);
    }

    public function updateQuestionCountUser($id){
        $this->db->select()
            ->from('user')
            ->where("id = ?");

        $this->db->execute([$id]);

        $res = $this->db->fetchInto($this);

        $currentQuestions = $res->questions;
        $values = [$currentQuestions + 1, $id];
        $keys = ['questions'];
        $this->db->update(
            'user',
            $keys,
            "id = ?"
        );

        return $this->db->execute($values);
    }

    public function updateAnswerCountUser($id){
        $this->db->select()
            ->from('user')
            ->where("id = ?");

        $this->db->execute([$id]);

        $res = $this->db->fetchInto($this);

        $currentAnswerCount = $res->user_answers;
        $values = [$currentAnswerCount + 1, $id];
        $keys = ['user_answers'];
        $this->db->update(
            'user',
            $keys,
            "id = ?"
        );

        return $this->db->execute($values);
    }

    public function updateAnswerCountQuestion($id){
        $this->db->select()
            ->from('question')
            ->where("question_id = ?");

        $this->db->execute([$id]);

        $res = $this->db->fetchInto($this);

        $currentAnswerCount = $res->answers;
        $values = [$currentAnswerCount + 1, $id];
        $keys = ['answers'];
        $this->db->update(
            'question',
            $keys,
            "question_id = ?"
        );

        return $this->db->execute($values);
    }

    public function updateUserActivity($id){
        $now = gmdate('Y-m-d H:i:s');
        $keys = ['latest_activity'];
        $values = [$now, $id];

        $this->db->update(
            'user',
            $keys,
            "id = ?"
        );

        return $this->db->execute($values);
    }

    public function upvote($id){

        $this->db->select()
            ->from($this->getSource())
            ->where("question_id = ?");

        $this->db->execute([$id]);

        $res = $this->db->fetchInto($this);

        $currentUpvotes = $res->upvote;


        $values = [$currentUpvotes + 1, $id];
        $keys = ['upvote'];
        $this->db->update(
            'question',
            $keys,
            "question_id = ?"
        );

        return $this->db->execute($values);
    }

    public function downvote($id){
        $this->db->select()
            ->from($this->getSource())
            ->where("question_id = ?");

        $this->db->execute([$id]);

        $res = $this->db->fetchInto($this);

        $currentDownvotes = $res->downvote;


        $values = [$currentDownvotes + 1, $id];
        $keys = ['downvote'];
        $this->db->update(
            'question',
            $keys,
            "question_id = ?"
        );

        return $this->db->execute($values);
    }

    public function acceptAnswer($id){

        $this->db->select()
            ->from('comments')
            ->join('question', 'comments.question_id = question.question_id')
            ->where("comment_id = ?");

        $this->db->execute([$id]);

        $res = $this->db->fetchInto($this);
        $question_id = $res->question_id;
        $acceptedStatus = $res->accepted_answer;


        if($res->accepted == 1){

            $values = [0, $id];
            $keys = ['accepted'];
            $this->db->update(
                'comments',
                $keys,
                "comment_id = ?"
            );

            $this->db->execute($values);

            $this->setAcceptedAnswer($question_id, $acceptedStatus);


        } else {
            if($acceptedStatus != 1){

                $values = [1, $id];
                $keys = ['accepted'];
                $this->db->update(
                    'comments',
                    $keys,
                    "comment_id = ?"
                );
                $this->db->execute($values);

                $this->setAcceptedAnswer($question_id, $acceptedStatus);

            }

        }
    }

    public function setAcceptedAnswer($id, $acceptedStatus){
        if($acceptedStatus == 1){
            $values = [0, $id];
            $keys = ['accepted_answer'];
            $this->db->update(
                'question',
                $keys,
                "question_id = ?"
            );
        } else {
            $values = [1, $id];
            $keys = ['accepted_answer'];
            $this->db->update(
                'question',
                $keys,
                "question_id = ?"
            );
        }

        return $this->db->execute($values);
    }

}