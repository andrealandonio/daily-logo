<?php
/**
 * Class Daily_Logo
 *
 * Gets the logo info object
 */

class Daily_Logo {

	/**
	 * Logo ID
	 *
	 * @var int $id
	 */
	public $id;

	/**
	 * Blog ID
	 *
	 * @var int $blog_id
	 */
	public $blog_id;

	/**
	 * Logo name (used also for title and alt text)
	 *
	 * @var string $name
	 */
	public $name;

	/**
	 * Logo year_start
	 *
	 * @var int $year_start
	 */
	public $year_start;

	/**
	 * Logo month_start
	 *
	 * @var int $month_start
	 */
	public $month_start;

	/**
	 * Logo day_start
	 *
	 * @var int $day_start
	 */
	public $day_start;

    /**
     * Logo hour_start
     *
     * @var int $hour_start
     */
    public $hour_start;

    /**
     * Logo minute_start
     *
     * @var int $minute_start
     */
    public $minute_start;

    /**
     * Logo year_end
     *
     * @var int $year_end
     */
    public $year_end;

    /**
     * Logo month_end
     *
     * @var int $month_end
     */
    public $month_end;

    /**
     * Logo day_end
     *
     * @var int $day_end
     */
    public $day_end;

    /**
     * Logo hour_end
     *
     * @var int $hour_end
     */
    public $hour_end;

    /**
     * Logo minute_end
     *
     * @var int $minute_end
     */
    public $minute_end;

	/**
	 * Logo link
	 *
	 * @var string $link
	 */
	public $link;

	/**
	 * Logo link target
	 *
	 * @var int $target
	 */
	public $target;

	/**
	 * Logo image
	 *
	 * @var string $image
	 */
	public $image;

	/**
	 * Logo alternative image
	 *
	 * @var string $image_alternative
	 */
	public $image_alternative;

	/**
	 * Logo CSS class
	 *
	 * @var string $class
	 */
	public $class;

	/**
	 * __construct from result set
	 *
	 * @param $rs
	 */
	public function __construct( $rs ) {
		$this->id = (int) $rs->id;
		$this->blog_id = (int) $rs->blog_id;
		$this->name = trim( strip_shortcodes( strip_tags( (string) $rs->logo_name ) ) );
		$this->year_start = (int) $rs->logo_year_start;
		$this->month_start = (int) $rs->logo_month_start;
		$this->day_start = (int) $rs->logo_day_start;
        $this->hour_start = (int) $rs->logo_hour_start;
        $this->minute_start = (int) $rs->logo_minute_start;
        $this->year_end = (int) $rs->logo_year_end;
        $this->month_end = (int) $rs->logo_month_end;
        $this->day_end = (int) $rs->logo_day_end;
        $this->hour_end = (int) $rs->logo_hour_end;
        $this->minute_end = (int) $rs->logo_minute_end;
		$this->link = (string) $rs->logo_link;
		$this->target = (int) $rs->logo_target;
		$this->image = (string) $rs->logo_image;
		$this->image_alternative = (string) $rs->logo_image_alternative;
		$this->class = (string) $rs->logo_class;
	}
} 