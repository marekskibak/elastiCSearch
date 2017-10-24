

<div style="width: 30%; margin-left: auto; margin-right: auto;">

<div class="jsError"></div>
    
<?php echo validation_errors(); ?>

<?php echo form_open('user/registration', array('class'=>'jsform')); ?>
<div class="form-group">
<label for="name">User name:</label>
<input class="form-control" type="text" name="username" value="" size="50" />
</div>
<div class="form-group">
<label for="pwd">Password:</label>
<input class="form-control" type="password" name="password" value="" size="50" />
</div>
<div class="form-group">
<label for="pwd">Password Confirm:</label>
<input class="form-control" type="password" name="passconf" value="" size="50" />
</div>
<div class="form-group">
<label for="email">Email Address:</label>    
<input class="form-control" type="email" name="email" value="" size="50" />
</div>
<div><input type="submit" value="Submit" /></div>
</form>
</div>  
</body>
<script>
 $(document).ready(function(){
     $('form.jsform').on('submit',function(form){
         form.preventDefault();
         $.post('registration', $('form.jsform').serialize(), function(data){
        $('div.jsError').html(data);
         }
                 );
     });
     });
 </script> 
</html>