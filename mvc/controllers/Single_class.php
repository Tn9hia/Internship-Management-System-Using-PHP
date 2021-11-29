<?php

/**
 * single class controller
 */
class Single_class extends Controller
{
	
	public function index($id = '')
	{
		// code...
		$errors = array();
		if(!Auth::access('student'))
		{
			$this->redirect('access_denied');
		}

		$classes = new Classes_model();
		$row = $classes->first('class_id',$id);

		$crumbs[] = ['Dashboard',''];
		$crumbs[] = ['classes','classes'];

		if($row){
			$crumbs[] = [$row->class,''];
		}

		$limit = 10;
 		$pager = new Pager($limit);
 		$offset = $pager->offset;

		$page_tab = isset($_GET['tab']) ? $_GET['tab'] : 'lecturers';
		$lect = new Lecturers_model();

		$results = false;

		if($page_tab == 'lecturers'){
			
			//display lecturers
			$query = "select * from topic_lecturers where topic_id = :topic_id && disabled = 0 order by id desc limit $limit offset $offset";
			$lecturers = $lect->query($query,['class_id'=>$id]);

			$data['lecturers'] 		= $lecturers;
		}else
		if($page_tab == 'students'){
			
			//display lecturers
			$query = "select * from topic_students where topic_id = :topic_id && disabled = 0 order by id desc limit $limit offset $offset";
			$students = $lect->query($query,['class_id'=>$id]);

			$data['students'] 		= $students;
		}

		$data['row'] 		= $row;
 		$data['crumbs'] 	= $crumbs;
		$data['page_tab'] 	= $page_tab;
		$data['results'] 	= $results;
		$data['errors'] 	= $errors;
		$data['pager'] 		= $pager;

		$this->view('single-class',$data);
	}

	public function lectureradd($id = '')
	{

		$errors = array();
		if(!Auth::logged_in())
		{
			$this->redirect('login');
		}

		$classes = new Classes_model();
		$row = $classes->first('class_id',$id);

		$crumbs[] = ['Dashboard',''];
		$crumbs[] = ['classes','classes'];

		if($row){
			$crumbs[] = [$row->class,''];
		}

		$page_tab = 'lecturer-add';
		$lect = new Lecturers_model();

		$results = false;
		
		if(count($_POST) > 0)
		{

			if(isset($_POST['search'])){

				if(trim($_POST['name']) != ""){

					//find lecturer
					$user = new User();
					$name = "%".trim($_POST['name'])."%";
					$query = "select * from users where (firstname like :fname || lastname like :lname) && rank = 'lecturer' limit 10";
					$results = $user->query($query,['fname'=>$name,'lname'=>$name,]);
				}else{
					$errors[] = "please type a name to find";
				}
			
			}else
			if(isset($_POST['selected'])){

				//add lecturer
				$query = "select disabled,id from topic_lecturers where user_id = :user_id && topic_id = :topic_id limit 1";
  
				if(!$check = $lect->query($query,[
					'user_id' => $_POST['selected'],
					'class_id' => $id,
				])){

					$arr = array();
	 				$arr['user_id'] 	= $_POST['selected'];
	 				$arr['class_id'] 	= $id;
					$arr['disabled'] 	= 0;
					$arr['date'] 		= date("Y-m-d H:i:s");

					$lect->insert($arr);

					$this->redirect('single_class/'.$id.'?tab=lecturers');

				}else{

					//check if user is active
					if(isset($check[0]->disabled))
					{
						if($check[0]->disabled)
						{

							$arr = array();
			 				$arr['disabled'] 	= 0;
 							$lect->update($check[0]->id,$arr);

							$this->redirect('single_class/'.$id.'?tab=lecturers');

						}else{

							$errors[] = "that lecturer already belongs to this class";
						}
					}else{
						$errors[] = "that lecturer already belongs to this class";
					}
						
				}
 
			}

		}

		$data['row'] 		= $row;
 		$data['crumbs'] 	= $crumbs;
		$data['page_tab'] 	= $page_tab;
		$data['results'] 	= $results;
		$data['errors'] 	= $errors;

		$this->view('single-class',$data);
	}


	public function lecturerremove($id = '')
	{

		$errors = array();
		if(!Auth::logged_in())
		{
			$this->redirect('login');
		}

		$classes = new Classes_model();
		$row = $classes->first('class_id',$id);


		$crumbs[] = ['Dashboard',''];
		$crumbs[] = ['classes','classes'];

		if($row){
			$crumbs[] = [$row->class,''];
		}

		$page_tab = 'lecturer-remove';
		$lect = new Lecturers_model();

		$results = false;
		
		if(count($_POST) > 0)
		{

			if(isset($_POST['search'])){

				if(trim($_POST['name']) != ""){

					//find lecturer
					$user = new User();
					$name = "%".trim($_POST['name'])."%";
					$query = "select * from users where (firstname like :fname || lastname like :lname) && rank = 'lecturer' limit 10";
					$results = $user->query($query,['fname'=>$name,'lname'=>$name,]);
				}else{
					$errors[] = "please type a name to find";
				}
			
			}else
			if(isset($_POST['selected'])){

				//add lecturer
				$query = "select id from topic_lecturers where user_id = :user_id && topic_id = :topic_id && disabled = 0 limit 1";
 
				if($row = $lect->query($query,[
					'user_id' => $_POST['selected'],
					'class_id' => $id,
				])){

					$arr = array();
						$arr['disabled'] 	= 1;

					$lect->update($row[0]->id,$arr);

					$this->redirect('single_class/'.$id.'?tab=lecturers');

				}else{
					$errors[] = "that lecturer was not found in this class";
				}
 
			}

		}

		$data['row'] 		= $row;
 		$data['crumbs'] 	= $crumbs;
		$data['page_tab'] 	= $page_tab;
		$data['results'] 	= $results;
		$data['errors'] 	= $errors;

		$this->view('single-class',$data);
	}


	public function studentadd($id = '')
	{

		$errors = array();
		if(!Auth::logged_in())
		{
			$this->redirect('login');
		}

		$classes = new Classes_model();
		$row = $classes->first('class_id',$id);

		$crumbs[] = ['Dashboard',''];
		$crumbs[] = ['classes','classes'];

		if($row){
			$crumbs[] = [$row->class,''];
		}

		$page_tab = 'student-add';
		$stud = new Students_model();

		$results = false;
		
		if(count($_POST) > 0)
		{

			if(isset($_POST['search'])){

				if(trim($_POST['name']) != ""){

					//find student
					$user = new User();
					$name = "%".trim($_POST['name'])."%";
					$query = "select * from users where (firstname like :fname || lastname like :lname) && rank = 'student' limit 10";
					$results = $user->query($query,['fname'=>$name,'lname'=>$name,]);
				}else{
					$errors[] = "please type a name to find";
				}
			
			}else
			if(isset($_POST['selected'])){

				//add student
				$query = "select disabled,id from topic_students where user_id = :user_id && topic_id = :topic_id limit 1";
  
				if(!$check = $stud->query($query,[
					'user_id' => $_POST['selected'],
					'class_id' => $id,
				])){

					$arr = array();
	 				$arr['user_id'] 	= $_POST['selected'];
	 				$arr['class_id'] 	= $id;
					$arr['disabled'] 	= 0;
					$arr['date'] 		= date("Y-m-d H:i:s");

					$stud->insert($arr);

					$this->redirect('single_class/'.$id.'?tab=students');

				}else{

					//check if user is active
					if(isset($check[0]->disabled))
					{
						if($check[0]->disabled)
						{

							$arr = array();
			 				$arr['disabled'] 	= 0;
 							$stud->update($check[0]->id,$arr);

							$this->redirect('single_class/'.$id.'?tab=students');

						}else{

							$errors[] = "that student already belongs to this class";
						}
					}else{
						$errors[] = "that student already belongs to this class";
					}
 				}
 
			}

		}

		$data['row'] 		= $row;
 		$data['crumbs'] 	= $crumbs;
		$data['page_tab'] 	= $page_tab;
		$data['results'] 	= $results;
		$data['errors'] 	= $errors;

		$this->view('single-class',$data);
	}


	public function studentremove($id = '')
	{

		$errors = array();
		if(!Auth::logged_in())
		{
			$this->redirect('login');
		}

		$classes = new Classes_model();
		$row = $classes->first('class_id',$id);


		$crumbs[] = ['Dashboard',''];
		$crumbs[] = ['classes','classes'];

		if($row){
			$crumbs[] = [$row->class,''];
		}

		$page_tab = 'student-remove';
		$stud = new Students_model();

		$results = false;
		
		if(count($_POST) > 0)
		{

			if(isset($_POST['search'])){

				if(trim($_POST['name']) != ""){

					//find student
					$user = new User();
					$name = "%".trim($_POST['name'])."%";
					$query = "select * from users where (firstname like :fname || lastname like :lname) && rank = 'student' limit 10";
					$results = $user->query($query,['fname'=>$name,'lname'=>$name,]);
				}else{
					$errors[] = "please type a name to find";
				}
			
			}else
			if(isset($_POST['selected'])){

				//add student
				$query = "select id from topic_students where user_id = :user_id && topic_id = :topic_id && disabled = 0 limit 1";
 
				if($row = $stud->query($query,[
					'user_id' => $_POST['selected'],
					'class_id' => $id,
				])){

					$arr = array();
						$arr['disabled'] 	= 1;

					$stud->update($row[0]->id,$arr);

					$this->redirect('single_class/'.$id.'?tab=students');

				}else{
					$errors[] = "that student was not found in this class";
				}
 
			}

		}

		$data['row'] 		= $row;
 		$data['crumbs'] 	= $crumbs;
		$data['page_tab'] 	= $page_tab;
		$data['results'] 	= $results;
		$data['errors'] 	= $errors;

		$this->view('single-class',$data);
	}




	
}
