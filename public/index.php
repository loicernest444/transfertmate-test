<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <?php
        header("refresh: 1000;");
        header('Content-type: text/html; charset=utf-8');
    ?>
    <title>Document</title>
</head>
<body>
    <?php
        include_once('../Database/Connection.class.php');
        include_once('../Database/ManageTable.class.php');
        include_once('../Model/Book.class.php');
        try{
            $pdo = Connection::get()->connect();
            $manageTables = new ManageTable($pdo);
            //$manageTables->insertAuthor("wambi");
            //$manageTables->dropTables();
            $totalitems = $manageTables->getNumberOfInput();
            $limit = Connection::getLimit();
            $totalPages = ceil($totalitems/$limit);
            $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
            $paginationStart = ($page-1)*$limit;

            if(empty($_POST['submit']))
            {
            }else
            {
                if(empty($_POST['input'])){
                    $file = $_FILES['fileUpload'];

                    $fileName = $_FILES['fileUpload']['name'];
                    $fileTmpName = $_FILES['fileUpload']['tmp_name'];
                    $fileSize = $_FILES['fileUpload']['size'];
                    $fileError = $_FILES['fileUpload']['error'];
                    $fileType = $_FILES['fileUpload']['type'];

                    $fileExt = explode('.',$fileName);
                    $fileActualExt = strtolower(end($fileExt));

                    $allowed = array('xml');

                    //check if file has .xml extension
                    if (in_array($fileActualExt, $allowed)) {
                        
                        if ($fileError === 0) {

                            //check if file is less than 500Mb we can upload xml file
                            if($fileSize < 500000){
                                $fileNameNew = uniqid("",true).".".$fileActualExt;
                                $fileDestination = '../XML/'.$fileNameNew;
                                move_uploaded_file($fileTmpName,$fileDestination);
                                $recordBook = new Book($pdo);
                                $recordBook->recordBook($fileDestination);
                                header("Location: #?uploadsuccess");
                            }else{
                                echo "<div class='message'>your file is too big!</div>";
                            }
                        }else{
                            echo "<div class='message'> there was an error uploading your file !</div>";
                        }

                    }else{
                        echo "<div class='message'> you cannot upload files of this type !</div>";
                    }    
                }
                
            }
            if(empty($_POST['input']))
            {
                $books = $manageTables->getBooks($paginationStart,$limit);
            }else
            {
                $input = $_POST['input'];
                $books = $manageTables->getBooks($paginationStart,$limit,$input);
            }
        }catch(\PDOException $e)
        {
            echo $e->getMessage();
        } 
    ?>
     <div class="book">
        <h2> 
            <center> Book Listing</center>
        </h2>
        <div>
            <form action="#" method="POST" enctype="multipart/form-data">
                    <input type="file" name="fileUpload" id="fileUpload" accept=".xml" />
                    <input type="text" name="input" id="text" placeholder="search by author">
                    <input class="submit" name="submit" type="submit" value="submit">
            </form>
        </div>
        <div id="row">
            <div class="animation" style="background-color: aqua;">
                <div class="table">
                    <div class="row">Author</div>
                    <div class="row">Book</div>
                </div>                
            </div>
            <?php
                if(!empty($_POST['input']))
                {
                    foreach($books as $row){
                        $author = $manageTables->getAuthorByName($row['nameauthor']);
            ?>
                <div class="animation">
                    <div class="table">
                        <div class="row">
                            <?php
                                if(empty($row['nameauthor'])){
                                    echo htmlspecialchars("<none>(no author found)");
                                }else{
                                    echo $row['nameauthor'];
                                }
                            ?>
                        </div>
                        <div class="row">
                            <?php
                                if(empty($row['namebook'])){
                                    echo htmlspecialchars("<none> (no books found)");
                                }else{
                                    echo $row['namebook'];
                                }
                            ?>
                        </div>
                    </div>                
                </div>
            <?php
                    }
                }
            ?>
            <?php
                foreach($books as $row){
            ?>
                <div class="animation">
                    <div class="table">
                        <div class="row">
                            <?php
                                if(empty($row['nameauthor'])){
                                    echo htmlspecialchars("<none>(no author found)");
                                }else{
                                    echo $row['nameauthor'];
                                }
                            ?>
                        </div>
                        <div class="row">
                            <?php
                                if(empty($row['namebook'])){
                                    echo htmlspecialchars("<none> (no books?? found)");
                                }else{
                                    echo $row['namebook'];
                                }
                            ?>
                        </div>
                    </div>                
                </div>
            <?php
                }
            ?>
        </div>        
    </div>
    <div class="pagination">
        <?php
            if($page == 1){

            }else{

                echo "<a href='?page=".($page-1)."' class='normal'> << preview </a>";
            }

            for($i=1; $i<=$totalPages; $i++)
            {
                if($i == $page)
                {
                    echo "<a href='?page=".$i."' class='active'>".$i."</a>";
                }else
                {
                    echo "<a href='?page=".$i."' class='normal'>".$i."</a>";
                }
            }
            if($page == $totalPages || $totalPages==0){

            }else{

                echo "<a href='?page=".($page+1)."' class='normal'> next >> </a>";
            }
        ?>
    </div>
    <script src="javaScript.js"></script>
</body>
</html>