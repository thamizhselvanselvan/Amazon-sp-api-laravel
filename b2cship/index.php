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
                    &nbsp;
            </div>
        </div>

    </form>
    <div class= " " style= "margin: 0rem 3rem 0rem 3rem; " id="summry">
      <div class=" row border border-dark " >
        <div class= "col-3">Status </div>
       <div class= "col-3">AWB No </div>
       <div class= "col-3">Booking Date </div>
       <div class= "col-3 text-primary" style="cursor:pointer" id="more_details">More Details </div>
    </div>
    <div class= "row border border-dark"  id="show_table">
    
      <div class= "col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3" style= "">
      <span class="font-weight-normal"> Packet Details</span>
        <table class="table table-sm table-striped table-bordered " style="font-size: 12px;">
            <tbody>
                <tr>
                    <td> AWB No </td>
                    <td class="TrackingNumber">   </td>
                </tr>
                <tr>
                    <td> Origin </td>
                    <td> </td>
                </tr>
                <tr>
                    <td> Destination </td>
                    <td class="Destination">   </td>
                </tr>
                <tr>
                    <td> Consignor </td>
                    <td>   </td>
                </tr>
                <tr>
                    <td> Consignee </td>
                    <td> </td>
                </tr>
                <tr>
                    <td> Address of Consignee </td>
                    <td></td>
                </tr>
            </tbody>
        </table>  


      </div>
     
        <div class= "col-12 col-sm-12 col-md-9 col-lg-9 col-xl-9">
           <strong> Tracking History </strong> 
            <table class="table table-sm table-striped "  style= "border: black solid 1px; font-size: 15px;"> 
                <thead >
                    <tr>
                        <th scope= "col" > Date</th>
                        <th scope= "col" > Location</th>
                        <th scope= "col" > Activities</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    
                </tbody>
            </table>
        </div>
    </div>
    </div>


</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script>
        $('#summry').hide();
        $('#show_table').hide();
        $('#more_details').on('click',function()
        {
            $('#show_table').toggle();
        })
         $("#submit").on('click',function(e)
        {
            e.preventDefault();
            $count=0;
            $data= $('#AWB_No').val()
        //   

            let table = $(".table");
            // alert($data);
            $.ajax({
                type: 'POST',
                url: 'GetData.php',
                data: {data: $data},
                success: function(response){
                    
                

                    let data = JSON.parse (response);
                    let td = "";
                    console.log(data);
                   
                     $.each(data, function(key, value){
                       
                       if($count != 0 ){
                        td += "<tr>";
                            td += "<td>"+ value[1].EventDateTime +"</td>";
                            td += "<td>"+ value[2].EventCity +"</td>";
                            td += "<td>"+ value[0].EventReason +"</td>";
                        td += "</tr>";
                       }
                       else
                       {
                           table.find(".TrackingNumber").text(value[0].TrackingNumber);
                           table.find(".Destination").text(value[1].City+', '+value[3].CountryCode);
                       }
                       $count++;
                     });

                     
                     table.find('#tbody').html(td);
                     $('#summry').show();
                }
                
            })

        });
    </script>

</body>
</html>
