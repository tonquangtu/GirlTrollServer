<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CommentController extends Controller {

	public function postComment(Request $request){
		
		// Get data send by client
		$memberId = $request->input('memberId');
		$feedId = $request->input('feedId');
		$comment = $request->input('comment');

		// If member not login
		if($memberId==''){
			return Response::json([
				'success'=>0,
				'message'=>'Not login'
			]);
		}else{
			$member = Member::where('member_id',$memberId)->first();
			if(isset($member->id)){
				$idMember = $member->id;
			}else{
				return Response::json([
					'success'=>0,
					'message'=>'This is not member of girltroll'
				]);
			}
			// Add comment of member in table Comment
			$object = new Comment;
			$object->member_id = $idMember;
			$object->feed_id = $feedId;
			$object->comment = $comment;
			$object->save();
			return Response::json([
				'success'=>1,
				'message'=>'Success'
			]);
		}
	}

	public function loadComment(Request $request){
		//Get data send by client
		$memberId = $request->input('memberId');
		$feedId = $request->input('feedId');
		$currentCommentId = $request->input('currentCommentId');
		$limit = $request->input('limit');

		//If member not login
		if($memberId==''){
			$idMember = '';
		}else{
			$member = Member::where('member_id',$memberId)->first();
			if(isset($member->id)){
				$idMember = $member->id;
			}else{
				$idMember = '';
			}
		}

		//Error if end of comment of feed
		if($currentCommentId==Comment::where('feed_id',$feedId)->first()->id){
			$success     = 0;
			$data        = [];
			$afterFeedId = 0;
		} else{

			//currentCommentId == -1 => the first load comment of feed
			if($currentCommentId == -1){
				$current = Comment::where('feed_id',$feedId)->last()->id+1;
			} else{
				$current = $currentCommentId;
			}

			$comments = Comment::where('feed_id',$feedId)->where('id','<', $current)->orderBy('id','DESC')->take($limit)->get();
			$data = $this->getComment($comments, $idMember);

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

	public function refreshComment(Request $request){
		//Get data send by client
		$memberId = $request->input('memberId');
		$feedId = $request->input('feedId');
		$currentCommentId = $request->input('currentCommentId');
		$limit = $request->input('limit');


		//If member not login
		if($memberId==''){
			$idMember = '';		
		}else{
			$member = Member::where('member_id',$memberId)->first();
			if(isset($member->id)){
				$idMember = $member->id;
			}else{
				$idMember = '';
			}
		}
		//currentCommentId == -1 => the first load comment of feed
		//currentCommentId == max comment => this is newest comment of feed
		//else load $limit new comment of feed
		if($currentCommentId == -1){
			$comment = Comment::where('feed_id',$feedId)->orderBy('id','DESC')->take($limit)->get();
			
			$data = $this->getComment($comment, $idMember);

			$success = 1;
			$afterCommentId = (int)$comment->first()->id;
			$message = "Success";
		}else if($currentCommentId==Comment::where('feed_id', $feedId)->last()->id){
			$data = null;
			$success = 1;
			$afterCommentId = $currentCommentId;
			$message = "This is newest Comment Of Feed";
		} else {
			$comment = Comment::where('feed_id',$feedId)->where('id','>',$currentCommentId)->orderBy('id','ASC')->take($limit)->get();
			//Sort By DESC OF ID
			$comment->sortByDesc('id', $options = SORT_REGULAR);

			$data = $this->getComment($comment, $idMember);

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
	public function getComment($comments, $idMember){
		$data = array();
		foreach($comments as $item){

			$mem = $item->member()->first();
			$member= array();
			$member['memberId']   =$mem->member_id;
			$member['username']   =$mem->username;
			$member['rank']       =$mem->rank;
			$member['like']       =$mem->like;
			$member['avatarUrl']  =$mem->avatar_url;
			$member['totalImage'] =$mem->total_image ;


			$isLike = MemberLikeComment::where('member_id', $idMember)->where('comment_id',$item->id)->first();
			if(isset($isLike->id)){
				$liked = $isLike->is_like;
			}else{
				$liked = 0;
			}

			$mdata = array();
			$mdata['commentId']  = $item->id;
			$mdata['numLike']   = $item->num_like;
			$mdata['isLike']  = $liked;
			$mdata['time']    = $item->created_at;
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
				'message'=>'Not login'
			]);
		}else{
			$member = Member::where('member_id',$memberId)->first();
			
			if(isset($member->id)){
				$idMember = $member->id;
			}else{
				return Response::json([
					'success'=>0,
					'message'=>'This is not member of girltroll'
				]);
			}
			$object = Comment::where('id',$commentId)->where('member_id',$idMember)->where('feed_id',$feedId)->first();
			
			// If this comment is not of member
			if(isset($object->id)){
				$object->delete();
				return Response::json([
					'success'=>1,
					'message'=>'Success'
				]);
			}else{
				return Response::json([
					'success'=>0,
					'message'=>'You can\' delete this comment'
				]);
			}
		}
	}
}
