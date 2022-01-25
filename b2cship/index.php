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

     <div class="fullTable"> </div>
    

</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script>
        // $('#summry').hide();
        // $('#show_table').hide();
        // $('#more_details').on('click',function()
        // {
        //     $('#show_table').toggle();
        // });
         $("#submit").on('click',function(e)
        {
            e.preventDefault();
            $count = 0;
            $data= $('#AWB_No').val()
            
        //   

            let table = $(".table");
            // alert($data);
            $.ajax({
                type: 'POST',
                url: 'GetData.php',
                data: {data: ($data)},
                success: function(response){
                   
                    let data = JSON.parse((response));
                 
                    let td = "";
                    let table = "";
                    
                    let TrackingNumber = '';
                    let City = '';
                    let Country = '';
                    
                    let EventReason = '';
                    let EventDate = '';
                    let EventCity = '';
                    

                    // console.log(data);

                $.each(data, function (key1, value1 ){
                      td= '';
                      

                show_table='<div class= "table" style= "padding: 0rem 3rem 0rem 3rem; " id="summry">';
                    show_table += ' <div class=" row border border-dark " > ';
                        show_table += ' <div class= "col-3">Status </div> ';
                        show_table += ' <div class= "col-3 AWB_No'+key1+'">AWB No:- </div> ';
                        show_table += ' <div class= "col-3 Booking_date'+key1+'">Booking Date </div> ';
                        show_table += ' <div class= "col-3 text-primary more_details" style="cursor:pointer">More Details </div> ';
                    show_table += ' </div>';
                
                show_table += ' <div class= "row border border-dark show_table"  style= "padding: 0rem 2.2rem 0rem 2.2rem; " id="show_table"> ';
                
                show_table += ' <div class= "col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3" > ';
                show_table += ' <span class="font-weight-normal"> Packet Details</span> ';
                    show_table += ' <table class="table table-sm table-striped table-bordered " style="font-size: 12px;"> ';
                        show_table += ' <tbody> ';
                            show_table += ' <tr> ';
                                show_table += ' <td> AWB No </td> ';
                                show_table += ' <td class="TrackingNumber'+key1+'">   </td> ';
                            show_table += ' </tr> ';
                            show_table += ' <tr> ';
                                show_table += ' <td> Origin </td> ';
                                show_table += ' <td> </td> ';
                            show_table += ' </tr> ';
                            show_table += ' <tr> ';
                                show_table += ' <td> Destination </td> ';
                                show_table += ' <td class="Destination'+key1+'">   </td> ';
                            show_table += ' </tr> ';
                            show_table += ' <tr> ';
                                show_table += ' <td> Consignor </td> ';
                                show_table += ' <td>   </td> ';
                            show_table += ' </tr> ';
                            show_table += ' <tr> ';
                                show_table += ' <td> Consignee </td> ';
                                show_table += ' <td> </td> ';
                            show_table += ' </tr> ';
                            show_table += ' <tr> ';
                                show_table += ' <td> Address of Consignee </td> ';
                                show_table += ' <td></td> ';
                            show_table += ' </tr> ';
                        show_table += ' </tbody> ';
                    show_table += ' </table>   ';


                show_table += ' </div> ';
                
                    show_table += ' <div class= "col-12 col-sm-12 col-md-9 col-lg-9 col-xl-9"> ';
                    show_table += ' <strong> Tracking History </strong>  ';
                        show_table += ' <table class="table table-sm table-striped "  style= "border: black solid 1px; font-size: 15px;">  ';
                            show_table += ' <thead > ';
                                show_table += ' <tr> ';
                                    show_table += ' <th scope= "col" > Date</th> ';
                                    show_table += ' <th scope= "col" > Location</th> ';
                                    show_table += ' <th scope= "col" > Activities</th> ';
                                show_table += ' </tr> ';
                            show_table += ' </thead> ';
                            show_table += ' <tbody class="tbody'+key1+'">';
                                                
                            show_table += ' </tbody> ';
                        show_table += ' </table> ';
                    show_table += ' </div> ';
            show_table += ' </div> ';
            show_table += ' </div> ';

            $(".fullTable").append(show_table);
           
                     $.each(value1, function(key, value){
                       if($count===0){
                       
                           TrackingNumber = value[0].TrackingNumber;
                           City = value[1].City;
                           Country = value[3].CountryCode;
                           
                           $('.TrackingNumber'+key1).text(TrackingNumber);
                           $('.AWB_No'+key1).append(TrackingNumber);
                           $('.Destination'+key1).text(City+', '+ Country);
                           
                        }
                        else
                        {
                            EventReason = value[0].EventReason;
                            myDate = value[1].EventDateTime;
                            EventDate = myDate.split('T')[0];
                            EventCity = value[2].EventCity;
                            
                            td += "<tr>";
                            td += "<td>"+ EventDate+"</td>";
                            td += "<td>"+ EventCity +"</td>";
                            td += "<td>"+ EventReason +"</td>";
                            td += "</tr>";
                            
                        }
                        $count = 1;
                        
                        
                    })
                    $('.Booking_date'+key1).append(EventDate);

                    $('.tbody'+key1).append(td);
                    $count = 0;
                    $('#summry').show();
                     $('.show_table').hide();
                    
                    
                });
            }
            
        });
        
    });
   
    </script>
        <script>  
        $(document).on('click', '.more_details', function()
         {
            let self = $(this);
            let show_more = self.parent().next();
            show_more.toggle();

        });
        </script>

</body>
</html>
