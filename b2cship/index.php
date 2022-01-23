`
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <title>Tracking </title>
    </head>

<body  >

  <div class="container-fluid">

    <form class="" style="padding: 4rem 3rem .5rem 3rem; "   >

        <div class="row border" style="background-color: rgb(219, 217, 217);">
    
            <div class="col">

                <div class=" form-group  p-3 ">
                    <label for="" class="fs-5">Track AWB No</label>
                        <div class="form-floating pt-2" >
                            <textarea class="form-control" placeholder="Leave a comment here" id="AWB_No"  ></textarea>
                                
                                <label for="floatingTextarea">Track</label>
                        </div>  
                    <label class= "py-2"for="" class="fs-6">For multiple queries use commas(,) eg: US524641027, AS05861041</label> 
                </div>

                    <div class="row px-3">
                        <div class="col ">
                            <button  type="submit" id= "submit" class="btn btn-primary px-4 fs-6" style="border-radius: 50px; background:rgb(240, 198, 10); color:black">Track &nbsp;  <i class="fa fa-chevron-right" aria-hidden="true"></i> </button>
                        </div>   
                    </div>

            </div>
        </div>

    </form>

    <div style= "margin: 0rem 3rem 0rem 3rem;" >
        <div style= "border: black solid 1px;">
            <table class="table table-sm table-striped ">
                <thead>
                    <tr>
                        <th scope= "col"> Data</th>
                        <th scope= "col"> Location</th>
                        <th scope= "col"> Activities</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    
    </div>


</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script>
         $("#submit").on('click',function(e)
        {
            e.preventDefault();
           
            $data= $('#AWB_No').val()
            alert($data);
            $.ajax({
                type: 'POST',
                url: 'GetData.php',
                data: {data: $data},
                success: function(responce){

                    alert(responce);

                }
            })

        });
    </script>


</body>
</html>
`