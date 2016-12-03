<?php
namespace Admin\Controller;
use Think\Controller;

class ContentController extends Controller {
    function index(){
        if(session('adminUser')){
            $conds = array();
            $page = $_REQUEST['p'] ? $_REQUEST['p'] : 1;
            $pageSize = 5;
            
            if($_GET['title']){
                $conds['title'] = $_GET['title'];
            }
            if($_GET['catid']){
                $conds['catid'] = intval($_GET['catid']);
            }
            
            $news = D('News')->getNews($conds,$page,$pageSize);
            $count = D('News')->getNewsCount($conds);
            
            $res = new \Think\Page($count,$pageSize);
            $pageRes = $res->show();
            
            $positions = D('Position')->getNormalPositions();
            $this->assign('positions',$positions);
            
            $this->assign('pageRes',$pageRes);
            $this->assign('news',$news);
           
            $this->assign('webSiteMenu',D('Menu')->getBarMenus());
            $this->display();
        }else{
            $this->redirect('/admin.php?c=login');
        }
    }
    public function add(){
        if($_POST){
            if(!isset($_POST['title']) || !$_POST['title']){
                return show(0,'文章标题不存在');
            }
            if(!isset($_POST['small_title']) || !$_POST['small_title']){
                return show(0,'短标题不存在');
            }
            if(!isset($_POST['catid']) || !$_POST['catid']){
                return show(0,'所属栏目不存在');
            }
            if(!isset($_POST['content']) || !$_POST['content']){
                return show(0,'内容不存在');
            }
            if(!isset($_POST['keywords']) || !$_POST['keywords']){
                return show(0,'关键字不存在');
            }
            
            if($_POST['newsid']){
                return $this->save();
            }
            $newsId = D('News')->insert($_POST);
            if($newsId){
                $newsContent['content'] = $_POST['content'];
                $newsContent['news_id'] = $newsId;
                $res = D("NewsContent")->insert($newsContent);
                if($res){
                    return show(1,"新增成功");
                }
                return show(1,"主表插入成功，附表插入失败");
            }else{
                return show(0,"新增失败");    
            }
            
        }else{
            $webSiteMenu = D('Menu')->getBarMenus();
            $titleFontColor = C('TITLE_FONT_COLOR');
            $copyFrom = C('COPY_FROM');
            
            $this->assign('webSiteMenu',$webSiteMenu);
            $this->assign('titleFontColor',$titleFontColor);
            $this->assign('copyFrom',$copyFrom);
            
            $this->display();
        }
    }
    public function edit(){
        $newsId = $_GET['id'];
        if(!$newsId){
            $this->redirect('/admin.php?c=content');
        }
        
        $news = D('News')->find($newsId);
        if(!$news){
            $this->redirect('/admin.php?c=content');
        }
        
        $newsContent = D('NewsContent')->find($newsId);
        if($newsContent){
            $news['content'] = $newsContent['content'];
        }
        
        $this->assign("titleFontColor",C("TITLE_FONT_COLOR"));
        $this->assign('webSiteMenu',D('Menu')->getBarMenus());
        $this->assign("copyFrom",C("COPY_FROM"));
        $this->assign('news',$news);
        $this->display();
    }
    public function save(){
        $newsId = $data['news_id'];
        unset($data['news_id']);
        
        try{
            $res = D('News')->updateById($newsId,$data);
            $newsContent['content'] = $dat['content'];
            $resSub = D('NewsContent')->updateNewsById($newsId,$newsContent);
            if($res === false || $resSub === false){
                return show(0,'更新失败');
            }
            return show(1,'更新成功');
        }catch(Exception $e){
            return show(0,$e->getMessage());
        }
    }
    public function setStatus(){
        if($_POST){
            $id = $_POST['id'];
            $status = array('status'=>$_POST['status']);
            try{
                $del = D("News")->updateById($id,$status);
                if($del){
                    return show(1,'操作成功');
                }
                return show(0,'操作失败');
            }catch(Exception $e){
                return show(0,$e->getMessage());
            }
        }
        return show(0,'没有提交数据');
    }
    
    public function push(){
        $jumpUrl = $_SERVER['HTTP_REFERER'];

        $positionId = intval($_POST['position_id']);
        $newsId = $_POST['push'];
        
        if(!$positionId){
            return show(0,'没有选择推荐位');
        }
        if(!$newsId || !is_array($newsId)){
            return show(0,'请选择推荐的文章ID进行推荐');
        }
        
        try{
            $news = D('News')->getNewsByNewsIdIn($newsId);
            if(!$news){
                return show(0,'没有相关内容');
            }
            
            foreach($news as $new){
                $data = array(
                    'position_id'=>$positionId,
                    'title'=>$new['title'],
                    'thumb'=>$new['thumb'],
                    'news_id'=>$new['news_id'],
                    'status'=>$new['status'],
                    'create_time'=>$new['create_time'],
                );
                $position = D('PositionContent')->insert($data);
            }
        }catch(Exception $e){
            return show(0,$e->getMessage());
        }
        
        return show(1,'推荐成功',array('jump_url'=>$jumpUrl));
    }
}