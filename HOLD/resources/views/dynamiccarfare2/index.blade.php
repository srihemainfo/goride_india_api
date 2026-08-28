@extends('dashboard-layout.index')

@section('content')

<input id='start' value='0' disabled>
<input id='end' value = 0>
<button id='addButton'>AddButton</button>

    <div >
        <table>
            <head>
                <tr> 
                    <th> START</th>
                    <th>END</th>
                    <th> Saloon</th>
                    <th> Action</th>
               </tr>
            </head>
            <tbody id='body'>
                
                
            </tbody>
        </table>
        
    </div>

<script>
$(document).ready(function(){
    
        var currentvalue = 1;
    $(document).on('click','#addButton', function(){
        
        
            var $startValue = parseFloat($('#start').val());
            var $endvalue = parseFloat($('#end').val());
            
            if($endvalue == '' || $endvalue == '' ){
                 swalalerterror('Please select the end value')
               
                return;
            }

            
            if($startValue > $endvalue){
                swalalerterror('start value not higher than end')
               
                return;
            }else if($startValue ==$endvalue ){
                swalalerterror('Start and end Not same value allowed')
                return
        
            }
            
             $("#body").append(`
                <tr id='current_${currentvalue}'>
                    <th>${$startValue} <input type='hidden' class="start_${currentvalue}" name='start[]' value="${$startValue}"></th>
                    <th>${$endvalue} <input type='hidden' name='end[]' value="${$endvalue}"></th>
                    <th><input type='hidden' name[]='saloon'> </th>
                    <th><button class='removeButton'>RemoveRow</button> </th>
                <tr>
             `);
             
             $('#start').val($endvalue+1);
             $('#end').val('');
             currentvalue++;
            
        
    })
    
      $(document).on('click','.removeButton', function(){
          
            var start = $('#start');
            var end = $('#end');
            
            var $startValue = $(this).closest('tr').find("input[name='start[]']").val();
            var $endvalue  = $(this).closest('tr').find("input[name='start[]']").val();
           var $position =  $(this).closest('tr').attr('id').slice(-1);
            // console.log($postion);
            if($position == 1){
                start.val(0)
                end.val($endvalue)
                
            }else{
                
                var closestTr = $(this).closest('tr'); // Get the closest <tr>
                var positionId = 'current_' + ($position - 1); // Create the ID string
                var closetEndGet = closestTr.siblings('#' + positionId).find("input[name='end[]']");
                var closetEndValueGet  = closetEndGet.val();
                var finalValue = $endvalue -  closetEndValueGet ;
                    closetEndGet.val(finalValue)
                console.log(closetEndValueGet,finalValue,$endvalue); 
                
                
        //         console.log( $(this).closest('tr').closest('#current_1').attr('id'));
        //         // console.log($('#current_'+ ($postion-1).)
                
        //   var closetEndValueGet =  $(this).closest('tr').closest('#current_'+ ($postion-1)).find("input[name='end[]']").val();
        // //   console.log(closetEndValueGet);
        //     var finalValue = $endvalue - closetEndValueGet ;
        //     $(this).closest('tr').closest('#current_'+ $postion-1).find("input[name='end[]']").val(finalValue);
            
                
            }


            // var value =  $(this).closest('tr').find("input[name='start']").val();
             
            // console.log(value);
        //   $(this).closest('tr').remove();
          
          
      });
    
})
    
</script>


@endsection

@section('custom_scripts')
    @include('carfares.partials.carfares_js')
@endsection