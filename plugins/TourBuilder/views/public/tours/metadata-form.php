<fieldset>
   <div class="field">
      <?php
      echo text( array( 'name' => 'title', 'id' => 'title',
         'class' => 'textinput' ), $tour->title, 'Tour Title' );
      echo form_error( 'title' );
      ?>
      <p class="explanation">
      A name given to a tour.Typically, a title will be a name
      by which the resource is formally known.
      </p>
   </div>

   <div class="field">
      <?php
      echo text( array( 'name' => 'slug', 'id' => 'slug',
         'class' => 'textinput' ), $tour->slug, 'Tour Slug' );
      echo form_error( 'slug' );
      ?>
      <p class="explanation">
      A url-compatible name making the tour's url more self-explanitory.
      The slug must not contain spaces or special characters.
      </p>
   </div>

   <div class="field">
      <?php
      echo text(array('name'=>'credits', 'id'=>'credits',
         'class'=>'textinput'), $tour->credits, 'Tour Credits' );
      ?>
      <p class="explanation">
      An entity primarily responsible for maintaining the tour. Typically a person
      or an organization.
      </p>
   </div>

   <div class="field">
      <?php
      echo textarea( array( 'name'=>'description', 'id'=>'description',
         'class'=>'textinput', 'rows'=>'10', 'cols'=>'40'),
         $tour->description, 'Tour Description' );
      ?>
      <p class="explanation">
      A description of the tour. Description may include, but is not limited to,
      an abstract or a list of scenes or a theme for the tour.
      </p>
   </div>
</fieldset>
