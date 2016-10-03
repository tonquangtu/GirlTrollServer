<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Member;
use App\Comment;
use Response;
use App\MemberLikeComment;

class CommentController extends Controller {

	/**
	 * Post Comment
	 * @pẩm  Request $request [description]
	 * @return [type]           [description]
	 */
	public function postComment(Request $request){
		
		// Get data send by client
		$memberId = $request->input('memberId');
		$feedId = $request->input('feedId');
		$comment = $request->input('comment');

		// If member not login
		if($memberId==''){
			return Response::json([
				'success'=>0,
				'message'=>'Not login',
				'data'=>null
			]);
		}else{
			
			// Add comment of member in table Comment
			$object = new Comment;
			$object->member_id = $memberId;
			$object->feed_id = $feedId;
			$object->comment = $comment;
			$object->num_like = 0;
			$object->time = date('Y-m-d H:i:s');
			$object->save();
			$feed = $object->feed()->first();
			$feed->comment++;
			$feed->vote = $feed->like*0.5 + $feed->comment*0.5;
			$feed->save();
			$member = $object->member()->first();
			return Response::json([
				'success'=>1,
				'message'=>'Success',
				'data'=>[
					'commentId'=>$object->id,
					'comment' => $comment,
					'numLike' => 0,
					'isLike' => 0,
					'time' => $object->time,
					'member'=>[
						'memberId'=>$memberId,
						'username'=>$member->username,
						'avatarUrl'=>$member->facebook_id==''?URLWEB.$member->avatar_url:$member->avatar_url
					]
				]
			]);
		}
	}

	/**
	 * Load Comment with limit
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function loadComment(Request $request){
		//Get data send by client
		$memberId = $request->input('memberId');
		$feedId = $request->input('feedId');
		$currentCommentId = $request->input('currentCommentId');
		$limit = $request->input('limit');

		
		//If not comment of feed
		if(count(Comment::where('feed_id',$feedId)->get())==0){
			$success     = 0;
			$data        = [];
			$afterCommentId = 0;
		}

		//Error if end of comment of feed
		elseif($currentCommentId==Comment::where('feed_id',$feedId)->orderBy('id','ASC')->first()->id){
			$success     = 0;
			$data        = [];
			$afterCommentId = 0;
		} else{

			//currentCommentId == -1 => the first load comment of feed
			if($currentCommentId == -1){
				$current = Comment::where('feed_id',$feedId)->orderBy('id','ASC')->get()->last()->id+1;
			} else{
				$current = $currentCommentId;
			}

			$comments = Comment::where('feed_id',$feedId)->where('id','<', $current)->orderBy('id','DESC')->take($limit)->get();
			$data = $this->getComment($comments, $memberId);

			$success = 1;
			$afterCommentId = (int)$comments->last()->id;
		}
		$send = [
			'success' => $success,
			'message' => ($success==0)?'End Of Comment':'Success',
			'data'    => $data,
			'paging'  => [
				'before' => $currentCommentId,
				'after'  => $afterCommentId
			]
		];
		return Response::json($send);
	}

	/**
	 * Load Refresh Comment
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function refreshComment(Request $request){
		//Get data send by client
		$memberId = $request->input('memberId');
		$feedId = $request->input('feedId');
		$currentCommentId = $request->input('currentCommentId');
		$limit = $request->input('limit');


		
		//If not comment of feed
		if(count(Comment::where('feed_id',$feedId)->get())==0){
			$success     = 0;
			$data        = [];
			$afterCommentId = 0;
		}

		//currentCommentId == -1 => the first load comment of feed
		//currentCommentId == max comment => this is newest comment of feed
		//else load $limit new comment of feed
		elseif($currentCommentId == -1){
			$comment = Comment::where('feed_id',$feedId)->orderBy('id','DESC')->take($limit)->get();
			
			$data = $this->getComment($comment, $memberId);

			$success = 1;
			$afterCommentId = (int)$comment->first()->id;
			$message = "Success";
		}elseif($currentCommentId==Comment::where('feed_id', $feedId)->orderBy('id','ASC')->get()->last()->id){
			$data = null;
			$success = 1;
			$afterCommentId = $currentCommentId;
			$message = "This is newest Comment Of Feed";
		} else {
			$comment = Comment::where('feed_id',$feedId)->where('id','>',$currentCommentId)->orderBy('id','ASC')->take($limit)->get();
			//Sort By DESC OF ID
			$comment->sortByDesc('id', $options = SORT_REGULAR);

			$data = $this->getComment($comment, $memberId);

			$success = 1;
			$afterFeedId = (int)$comment->first()->id;
			$message = "Success";
		}
		$send = [
			'success' => $success,
			'message' => $message,
			'data'    => $data,
			'paging'  => [
				'before' => $currentCommentId,
				'after'  => $afterCommentId
			]
		];
		return Response::json($send);
	}


	/**
	 * Get Comments Of Feed And Check Member Was Like
	 * @param  [type] $comments [description]
	 * @param  [type] $idMember [description]
	 * @return [type]           [description]
	 */
	public function getComment($comments, $memberId){
		$data = array();
		foreach($comments as $item){

			$mem = $item->member()->first();
			$member= array();
			$member['memberId']   =$mem->id;
			$member['username']   =$mem->username;
			// $member['rank']       =$mem->rank;
			$member['like']       =$mem->like;
			$member['avatarUrl']  =$mem->facebook_id==''?URLWEB.$mem->avatar_url:$mem->avatar_url;
			$member['totalImage'] =$mem->total_image ;

			//Set liked
			$isLike = MemberLikeComment::where('member_id', $memberId)->where('comment_id',$item->id)->first();
			if(isset($isLike->id)){
				$liked = $isLike->is_like;
			}else{
				$liked = 0;
			}

			$mdata = array();
			$mdata['commentId']  = $item->id;
			$mdata['numLike']   = $item->num_like;
			$mdata['comment'] = $item->comment;
			$mdata['isLike']  = $liked;
			$mdata['time']    = $item->time;
			$mdata['member']  = $member;

			$data[] = $mdata;
		}
		return $data;
	}

	/**
	 * Delete Comment
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function deleteComment(Request $request){
		
		//Get data send by client
		$memberId = $request->input('memberId');
		$feedId = $request->input('feedId');
		$commentId = $request->input('commentId');

		// If member not login
		if($memberId==''){
			return Response::json([
				'success'=>0,
				'message'=>'Bạn chưa đăng nhập'
			]);
		}else{
			// $member = Member::where('member_id',$memberId)->first();
			
			// if(isset($member->id)){
			// 	$idMember = $member->id;
			// }else{
			// 	return Response::json([
			// 		'success'=>0,
			// 		'message'=>'This is not member of girltroll'
			// 	]);
			// }
			$object = Comment::where('id',$commentId)->where('member_id',$memberId)->where('feed_id',$feedId)->first();
			
			// If this comment is not of member
			if(isset($object->id)){
				$feed = $object->feed()->first();
				$feed->comment--;
				$feed->save();
				$object->delete();

				return Response::json([
					'success'=>1,
					'message'=>'Success'
				]);
			}else{
				return Response::json([
					'success'=>0,
					'message'=>'Không thể xóa comment'
				]);
			}
		}
	}

	/**
	 * Update when like or unlike 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function likeComment(Request $request){
		// Get data form client
		$memberId = $request->input('memberId');
		$commentId = $request->input('commentId');
		$type = $request->input('type');

		// Member not login
		if($memberId==''){
			return Response::json([
			'success' => 0,
			'message' =>'Bạn chưa đăng nhập'
			]);
		}

		//Update like of comment
		$comment = Comment::find($commentId);
		if(isset($comment->id)){
			if($type==1){
				$comment->num_like++;
			}
			else{
				$comment->num_like--;
			}
			$comment->save();
		}

		//Get id of member has member_id = memberId
		//If this is first time member like feed then create 1 record on table
		//MemberLikeFeed else update is_like for record
		// $memberLike = Member::where('id',$memberId)->first();
		$isLike = MemberLikeComment::where('member_id',$memberId)->where('comment_id', $commentId)->first();
		
		if(isset($isLike->id)){
			$isLike->is_like=$type;
			$isLike->save();
		}else{
			$isLike=new MemberLikeComment;
			
			$isLike->member_id = $memberId;
			$isLike->comment_id = $commentId;
			$isLike->is_like = $type;
			$isLike->save();
		}
		// $success = $this->postUpdate($request, 'like');
		return Response::json([
			'success' => 1,
			'message' => 'Success'
			]);
	}
}
