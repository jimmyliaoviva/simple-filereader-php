<?php session_start(); ?>
<html>

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
</head>
<style>
	.wrapper {
		margin: 5rem;
		margin-top: 0;
	}
</style>

<body>
	<!-- Bootstrap js -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<!-- navbar -->
	<nav class="navbar navbar-light bg-light">
		<form class="container-fluid justify-content-end">
			<a href="./index.php?action=logout" class="btn btn-sm btn-outline-secondary" type="button">登出</a>
		</form>
	</nav>
	<div class="wrapper">
		<!-- 如果在 session 中沒有驗證 -->
		<?php if (!isset($_SESSION['auth'])) { ?>
			<!-- 重新輸入密碼 -->
			<div class="modal" id="modal" data-bs-backdrop="static" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">驗證</h5>
						</div>
						<div class="modal-body">
							<form action="index.php" method="POST">
								<label>請輸入密碼</label>
								<input name="password" type="password">
								<input type="submit" value="送出">
							</form>
						</div>
						<div class="modal-footer">
						</div>
					</div>
				</div>
			</div>
			<!-- 跳出驗證的彈跳視窗 -->
			<script type="text/javascript">
				var myModalEl = document.getElementById('modal')
				var modal = bootstrap.Modal.getOrCreateInstance(myModalEl)
				modal.show()
			</script>
		<?php } ?>

		<?php
		//簡單的線上檔案管理

		$path = "./";
		$ignoreFile = array();
		//密碼驗證
		if (isset($_POST['password'])) {
			$password = $_POST['password'];
			// 國小
			if ($password == 'EduE@0823') {
				$_SESSION['auth'] = 'elementary';
				header("location:./");
				// 國中
			} elseif ($password == 'EduJ@0823') {
				$_SESSION['auth'] = 'middle';
				header("location:./");
				// 高中
			} elseif ($password == 'EduS@0823') {
				$_SESSION['auth'] = 'high';
				header("location:./");
				// 大專
			} elseif ($password == 'EduU@0823') {
				$_SESSION['auth'] = 'college';
				header("location:./");
				// 萬用帳號
			} elseif ($password == 'Edu@0823' ||$password == 'Ncu@0823' ) {
				$_SESSION['auth'] = 'admin';
				header("location:./");
			} else {
				echo '<h1>錯誤密碼</h1>';
			}
		}


		// 如果這是已經驗證的帳號，則判定他是屬於哪一類
		if (isset($_SESSION['auth'])) {
			switch ($_SESSION['auth']) {
				case 'elementary':
					array_push($ignoreFile, '02. 國中', '03. 高中', '04. 大專');
					break;
				case 'middle':
					array_push($ignoreFile, '01. 國小', '03. 高中', '04. 大專');
					break;
				case 'high':
					array_push($ignoreFile, '01. 國小', '02. 國中', '04. 大專');
					break;
				case 'college':
					array_push($ignoreFile, '01. 國小', '02. 國中', '03. 高中');
					break;
			}
			//根據action的資訊值,做對應操作
			if (isset($_GET['action'])) {
				switch ($_GET['action']) {
					case "getFolder":
						$path = $_GET["folder"];
						break;
					case "logout":
						unset($_SESSION['auth']);
						header("location:./");
						break;
				}
			}

			//瀏覽指定目錄下的檔案,並使用圖示輸出

			//path目錄資訊的過濾,判斷path存在,並其是否是個目錄
			if (!file_exists($path) || !is_dir($path)) {
				die($path . "目錄無效!");
			}
			//輸出表頭資訊
			echo "<h3>{$path}目錄下的檔案資訊<h3>";
			echo "</tr>";

			//開啟這個目錄,並遍歷目錄下面的所有檔案
			$dir = opendir($path);
			if ($dir) {
				$i = 0;
				//遍歷目錄中的檔案,並輸出檔案的資訊
				echo "<div class='row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4'>";
				while ($f = readdir($dir)) {
					// 把不顯示的檔案放在這裡
					if ($f == "." || $f == ".." || $f == 'img' || $f == 'index.php' || in_array($f, $ignoreFile)) {
						continue; //跳出本次迴圈,繼續下一次遍歷。
					}
					$file = trim($path, "/") . "/" . $f;
					$i++;
					// 抓出檔案的副檔名
					$ext = pathinfo($path . '/' . $f, PATHINFO_EXTENSION);
					// 如果檔案是個資料夾，則讓他可以點進去
					if (is_dir($file)) {
						echo "  <div class='col text-center'>
									<div class='card h-100'>
									<img src='./img/folder-solid.svg' class='card-img-top' style='width:40%; margin:auto' alt='...'>
										<div class='card-body'>
											<h6 class='card-title'>{$f}</h6>
										</div>
										<a href='index.php?folder={$file}&action=getFolder' class='stretched-link'></a>
									</div>
			  					</div>";
					} else {
						echo "  <div class='col text-center'>
				<div class='card h-100'>";
						if ($ext == 'pdf') {
							echo "<img src='./img/file-pdf-solid.svg' class='card-img-top' style='width:30%; margin:auto ;margin-top:0.5em' alt='...'>";
						} elseif ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext =='JPG') {
							echo "<img src='./img/image-solid.svg' class='card-img-top' style='width:40%; margin:auto' alt='...'>";
						} elseif ($ext == 'doc' || $ext == 'docx' || $ext == 'DOC' || $ext =='DOCX') {
							echo "<img src='./img/file-word-solid.svg' class='card-img-top' style='width:30%; margin:auto' alt='...'>";
						} elseif ($ext == 'xlsx') {
							echo "<img src='./img/file-excel-solid.svg' class='card-img-top' style='width:30%; margin:auto' alt='...'>";
						} elseif ($ext == 'mp4' || $ext == 'mov' || $ext == 'MP4' || $ext == 'MOV') {
							echo "<img src='./img/file-video-solid.svg' class='card-img-top' style='width:30%; margin:auto' alt='...'>";
						} else {
							echo "<img src='./img/question-solid.svg' class='card-img-top' style='width:30%; margin:auto; margin-top:0.5em' alt='...'>";
						}

						echo "<div class='card-body'>
					<h6 class='card-title'>{$f}</h6>
				  </div>
				  <a href='{$path}/{$f}' class='stretched-link'></a>
				</div>
			  </div>";
					}
				}
				closedir($dir); //關閉目錄
				echo "</div>";
			}
		}
		?>
	</div>
</body>

</html>