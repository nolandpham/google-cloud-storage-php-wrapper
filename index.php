<?php
use cAc\GcsWrapper, Google\Cloud\ServiceBuilder;
$b = '';
$p = '';
$a = '';
$result = null;

$ops = array(

	1		=> 'Generate Object Only',
	2		=> 'Add ACL Entity to Bucket',
	3		=> 'Remove ACL Entity from Bucket',
	4		=> 'Get ACL Role for Entity from Bucket',
	5		=> 'Update ACL Entity Role for Bucket',
	6		=> 'Upload File to Bucket',
	7		=> 'Upload Large (>5MB) File to Bucket',
	8		=> 'Combine 2 Files in Bucket'

);

if( isset( $_POST['submit'] ) && "Submit" === $_POST['submit'] ) {
	
	if( isset( $_POST['projectid'] ) && isset( $_POST['bucketid'] ) && isset( $_POST['jsonkey'] ) ) {
	
		$b = $_POST['bucketid'];
		$p = $_POST['projectid'];
		$a = isset( $_POST['action'] ) ? intval( $_POST['action'] ) : 1;
		require_once( __DIR__ . '/GcsWrapper.php' );
		$storage_test = new cAc\GcsWrapper\GoogleCloudStorage( $p, $_POST['jsonkey'], $b );	
		switch( $a ) {
			
			case 1:
				break;
			case 2:
				if( isset( $_POST['aclEntityType'] ) && isset( $_POST['aclEntityValue'] ) && isset( $_POST['aclRole'] ) ) {
				
					if ('allUsers' !== $_POST['aclEntityType'] && 'allAuthenticatedUsers' !== $_POST['aclEntityType']) {
					
						$entity = $_POST['aclEntityType'] . $_POST['aclEntityValue'];
					}
					else {
					
						$entity = $_POST['aclEntityType'];
					
					}
					$role = $_POST['aclRole'];
					$result = $storage_test->bucket_acl_entity_add( $entity, $role );
				
				}
				break;
			case 3:
				if( isset( $_POST['aclEntityType'] ) && isset( $_POST['aclEntityValue'] ) ) {
				
					if ('allUsers' !== $_POST['aclEntityType'] && 'allAuthenticatedUsers' !== $_POST['aclEntityType'] ) {
					
						$entity = $_POST['aclEntityType'] . $_POST['aclEntityValue'];
					
					}
					else {
					
						$entity = $_POST['aclEntityType'];
					
					}
					$result = $storage_test->bucket_acl_entity_remove( $entity );
					
				}
				break;
			case 4:
				if( isset( $_POST['aclEntityType'] ) && isset( $_POST['aclEntityValue'] ) ) {
				
					if ('allUsers' !== $_POST['aclEntityType'] && 'allAuthenticatedUsers' !== $_POST['aclEntityType']) {
					
						$entity = $_POST['aclEntityType'] . $_POST['aclEntityValue'];
					
					}
					else {
					
						$entity = $_POST['aclEntityType'];
						
					}
					$result = $storage_test->bucket_acl_entity_get( $entity );
				
				}
				break;
			case 5:
				if( isset( $_POST['aclEntityType'] ) && isset( $_POST['aclEntityValue'] ) && isset( $_POST['aclRole'] ) ) {
				
					if ('allUsers' !== $_POST['aclEntityType'] && 'allAuthenticatedUsers' !== $_POST['aclEntityType']) {
					
						$entity = $_POST['aclEntityType'] . $_POST['aclEntityValue'];
					
					}
					else {
					 
						$entity = $_POST['aclEntityType'];
					
					}
					$role = $_POST['aclRole'];
					$result = $storage_test->bucket_acl_entity_update( $entity, $role );
					
				}
				break;
			case 6:
				if( isset( $_FILES['fileUpload'] ) && isset( $_POST['predefinedAcl'] ) ) {
				
					$target_dir = "uploads/";
					$target_file = $target_dir . basename($_FILES["fileUpload"]["name"]);
					$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
					if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $target_file)) {
						$result = $storage_test->bucket_upload_object( $target_file, $_FILES["fileUpload"]["name"], false, $permissions = $_POST['predefinedAcl'] );
					}
					else {
						$result = "Sorry, there was an error uploading your file.
						"
						. print_r( $_FILES, true );
					}
	
				}
				else {
					$result = "Required Field(s) empty.";
				}
				break;
			case 7:
				if( isset( $_FILES['fileUpload'] ) && isset( $_POST['predefinedAcl'] ) ) {
				
					$target_dir = "uploads/";
					$target_file = $target_dir . basename($_FILES["fileUpload"]["name"]);
					$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
					if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $target_file)) {
						$result = $storage_test->bucket_upload_large_object( $target_file, $_FILES["fileUpload"]["name"], false, $permissions = $_POST['predefinedAcl'] );
					}
					else {
						$result = "Sorry, there was an error uploading your file.
						"
						. print_r( $_FILES, true );
					}
	
				}
				else {
					$result = "Required Field(s) empty.";
				}
				break;
		
		}
	
	}

}
?>
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script src="js/test.js"></script>

<header class="container">
<h1>GCS Wrapper Test</h1>
<p>Enter the Project ID, Bucket ID, and content of the json file generated by Google's IAM, then submit to get results</p>
</header>
<content class="container">
	<form id="gcs-test-form" class="form-horizontal" method="post" enctype="multipart/form-data">

		<fieldset>

		<!-- Form Name -->
		<legend>Test GCS</legend>

		<!-- Text input-->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="projectid">Project ID</label>  
		  <div class="col-md-4">
		  <input id="projectid" name="projectid" type="text" placeholder="project-12345" class="form-control input-md" <?php echo empty( $p ) ? '' : 'value="' . $p . '"'?> >
		  <span class="help-block">enter the id, not the name</span>  
		  </div>
		</div>

		<!-- Text input-->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="bucketid">Bucket ID</label>  
		  <div class="col-md-4">
		  <input id="bucketid" name="bucketid" type="text" placeholder="your-bucket-id" class="form-control input-md" <?php echo empty( $b ) ? '' : 'value="' . $b . '"'?> >
		  <span class="help-block">enter the unique identifier for the bucket</span>  
		  </div>
		</div>

		<!-- Textarea -->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="jsonkey">JSON Key Content</label>
		  <div class="col-md-4">                     
			<textarea class="form-control" id="jsonkey" name="jsonkey"><?php echo isset( $_POST['jsonkey'] ) ? $_POST['jsonkey'] : '' ?></textarea>
		  </div>
		</div>
		
		<!-- Select Basic -->
		<div id="action-select" class="form-group">
		  <label class="col-md-4 control-label" for="action">Select an Operation</label>
		  <div class="col-md-4">
			<select id="action" name="action" class="form-control">
			<?php
			foreach( $ops as $key=>$value ) {
				$checked = '';
				if( $key === $a ) {
					$checked = ' selected="selected"';
				}
			  	echo '<option value="' . $key . '"' . $checked . '>' . $value . '</option>';
			}
			?>
			</select>
		  </div>
		</div>

		
		<div class="form-group">
		  <div class="col-md-4 col-md-offset-4">                     
			<input type="submit" class="form-control btn btn-primary" id="submit" name="submit" value="Submit"/>
		  </div>
		</div>

		</fieldset>

	</form>
	<div class="row">
		<div id="post-values" class="col-md-6 col-md-offset-3">
			<h2>Project: <?php echo $p; ?></h2>
			<h2>Bucket: <?php echo $b; ?></h2>
			<h2>Operation: <?php echo $ops[$a]; ?></h2>
		</div>
	</div>
	<div class="row">
                <div id="post-values" class="col-md-6 col-md-offset-3">
                        <h2><button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#showObject" aria-expanded="false" aria-controls="collapseExample">Full Object Output</button></h2>
                        <p id="showObject" class="collapse"><?php print_r( $storage_test ); ?></p>
                </div>
        </div>
</content>
<footer class="container">
	
	<div class="errors">
		<?php
		if( isset( $storage_test ) ) {
			
			echo '<h2>Errors:</h2><pre>';
			print_r( $storage_test->errors );
			echo '</pre>';
		
		}
		?>
	</div>
	
	<div class="result">
		<?php
		if (!empty( $result ) ) {
		
			echo '<h2>Result:</h2><pre>';
			print_r( $result );
			echo '</pre>';
		
		}
		?>
	</div>
	
</footer>
