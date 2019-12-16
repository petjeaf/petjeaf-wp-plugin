<?php

class Petje_Af_Main_Widget extends WP_Widget {
 
  function __construct() {
      parent::__construct(
          'Petje_Af_Main_Widget', 
          __('Petje.af widget', 'petje-af'), 
          array( 'description' => __( 'Widget to show direct links to your petje.af page', 'petje-af' ), ) 
      );
  }
  
  public function widget( $args, $instance ) {
      $title = apply_filters( 'widget_title', $instance['title'] );
      $page_slug = $instance['page_slug'];
      $min_amount = $instance['min_amount'];
      $onetime = $instance[ 'onetime' ] ? true : false;

      echo $args['before_widget'];

      if ( ! empty( $title ) )
      echo $args['before_title'] . $title . $args['after_title'];

      if ($page_slug) :

      ?>

      <style>
        .petje-af-widget {
          border: 1px solid rgba(0,0,0,.125);
          border-radius: 4px;
          font-family: 'Nunito', sans-serif !important;
          font-size: 16px;
        }

        .petje-af-widget p {
          font-weight: 400 !important;
          color: #6c7686 !important;
          font-size: 14px !important;
          line-height: 1.7;
          margin-top: 12px;
        }

        .petje-af-widget__title {
          margin-top: 0 !important;
          margin-bottom: 5px !important;
          font-weight: 700 !important;
          font-family: 'Nunito', sans-serif !important;
          font-size: 20px;
        }

        .petje-af-widget__amount {
          font-size: 20px !important;
          font-weight: 700 !important;
        }

        .petje-af-widget__amount span {
          color: #6c7686 !important;
          font-size: 16px !important;
        }

        .petje-af-widget__button {
          padding: 8px 16px !important;
          border-radius: 4px !important;
          background: #4cd964 !important;
          display: inline-block !important;
          color: #ffffff !important;
          font-weight: 700 !important;
          border: 1px solid #4cd964 !important;
          text-decoration: none !important;
          font-family: 'Nunito', sans-serif !important;
        }

        .petje-af-widget__button--info {
          background: transparent !important;
          border-color: #24b7fa !important;
          color: #24b7fa !important;
        }

        .petje-af-widget__button--info:hover,
        .petje-af-widget__button--info:active,
        .petje-af-widget__button--info:focus {
          color: #fff !important;
          background-color: #24b7fa !important;
          border-color: #24b7fa !important;
        }


        .petje-af-widget__button--cta:hover,
        .petje-af-widget__button--cta:active,
        .petje-af-widget__button--cta:focus {
          color: #fff !important;
          background-color: #2dd249 !important;
          border-color: #2ac845 !important;
        }

        .petje-af-widget__onetime {
          padding: 22px;
        }

        .petje-af-widget__members {
          padding: 22px;
          border-top: 1px solid rgba(0,0,0,.125);
        }

        .petje-af-widget__members.petje-af-widget__members--no-onetime {
          border-top: none;
        }

        .petje-af-widget__img-link {
          display: block;
          margin-bottom: 12px;
        }
      </style>

      <div class="petje-af-widget">

        <div class="petje-af-widget__inner">

          <?php if ($onetime) : ?>
            <div class="petje-af-widget__onetime">
              <a class="petje-af-widget__img-link" href="https://petje.af/<?= $page_slug; ?>/" target="_blank">
                <img style="width: 100px; height: auto" src="<?= plugin_dir_url( dirname(__FILE__) ); ?>public/images/petjeaf.png" alt="Petje af logo" />
              </a>
              <h5 class="petje-af-widget__title"><?php _e('Eenmalig doneren?', 'petje-af'); ?></h5>
              <p><?php _e('Neem eenmalig je petje af! Kies zelf het bedrag.', 'petje-af'); ?></p>
              <a class="petje-af-widget__button petje-af-widget__button--info" href="https://petje.af/<?= $page_slug; ?>/petjes/onetime" target="_blank"><?php _e('Eenmalig petje af!', 'petje-af'); ?></a>
            </div>
          <?php endif; ?>

          <?php if ($min_amount) : ?>
          <div class="petje-af-widget__members<?php if (!$onetime) : ?> petje-af-widget__members--no-onetime<?php endif; ?>">
              <?php if (!$onetime) : ?>    
              <a class="petje-af-widget__img-link" href="https://petje.af/<?= $page_slug; ?>/" target="_blank">
                <img style="width: 100px; height: auto" src="<?= plugin_dir_url( dirname(__FILE__) ); ?>public/images/petjeaf.png" alt="Petje af logo" />
              </a>  
              <?php endif; ?>
              <h5 class="petje-af-widget__title"><?php _e('Per maand vanaf', 'petje-af'); ?></h5>
              <span class="petje-af-widget__amount">€ <?= str_replace(',00', ',-', number_format($min_amount, 2, ',', '.')); ?> <span><?php _e('per maand', 'petje-af' ); ?></span></span>
              <p><?php _e('Word lid en steun ons al vanaf', 'petje-af'); ?> € <?= str_replace(',00', ',-', number_format($min_amount, 2, ',', '.')); ?> <span><?php _e('per maand', 'petje-af' ); ?></p>
              <a class="petje-af-widget__button petje-af-widget__button--cta" href="https://petje.af/<?= $page_slug; ?>/petjes" target="_blank"><?php _e('Neem je petje af!', 'petje-af'); ?></a>
          </div>
          <?php endif; ?>

        </div>

      </div>

      <?php

      endif;

      echo $args['after_widget'];
  }
          
  public function form( $instance ) {
    if ( isset( $instance[ 'title' ] ) ) {
        $title = $instance[ 'title' ];
    }
    else {
        $title = __( 'Steun ons via Petje af!', 'petje-af' );
    }
    if ( isset( $instance[ 'page_slug' ] ) ) {
      $page_slug = $instance[ 'page_slug' ];
    }
    else {
      $page_slug = '';
    }
    if ( isset( $instance[ 'min_amount' ] ) ) {
      $min_amount = $instance[ 'min_amount' ];
    }
    else {
      $min_amount = '';
    }
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">
            <?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'page_slug' ); ?>">
            <?php _e( 'Pagina:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'page_slug' ); ?>" name="<?php echo $this->get_field_name( 'page_slug' ); ?>" type="text" value="<?php echo esc_attr( $page_slug ); ?>" />
    </p>
    <p>
        <input class="checkbox" type="checkbox" <?php checked( $instance[ 'onetime' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'ontime' ); ?>" name="<?php echo $this->get_field_name( 'onetime' ); ?>" /> 
        <label for="<?php echo $this->get_field_id( 'onetime' ); ?>">Eenmalige betalingen activeren</label>
   </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'min_amount' ); ?>">
            <?php _e( 'Per maand vanaf:' ); ?></label>
        <input type="number" class="widefat" id="<?php echo $this->get_field_id( 'min_amount' ); ?>" name="<?php echo $this->get_field_name( 'min_amount' ); ?>" type="text" value="<?php echo esc_attr( $min_amount ); ?>" />
    </p>
  <?php 
  }
      
  public function update( $new_instance, $old_instance ) {
      $instance = array();
      $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
      $instance['min_amount'] = ( ! empty( $new_instance['min_amount'] ) ) ? strip_tags( $new_instance['min_amount'] ) : '';
      $instance['page_slug'] = ( ! empty( $new_instance['page_slug'] ) ) ? strip_tags( $new_instance['page_slug'] ) : '';
      $instance[ 'onetime' ] = $new_instance[ 'onetime' ];
      return $instance;
  }
}