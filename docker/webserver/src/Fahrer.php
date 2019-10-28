<?php

require_once './Page.php';

/**
 * TODO: Beschreibung Fahrer.php
 * 
 * @author   Julian Segeth
 * @author   Bican Gül 
 */
class Fahrer extends Page{

    /**
     * Creates a database connection.
     *
     * @return none
     */
    protected function __construct(){

        parent::__construct();
    }

    /**
     * Closes the database connection.
     *
     * @return none
     */
    protected function __destruct(){

        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is stored in an easily accessible way e.g. as associative array.
     *
     * @return none
     */
    protected function getViewData(){
        // to do: fetch data for this view from the database
    }

    /**
     * Generates the body section of the page.
     * 
     * @return none
     */
    protected function generatePageBody(){
echo <<< HTML
    <h1>Fahrer</h1>
    <section>
        <div>
            <p>Bestellung 1</p>
            <form action="https://echo.fbi.h-da.de" method ="get">
                <select name="driverStatusSelection" size="1">
                    <option value="inOvenOfCook">Noch im Ofen</option>
                    <option value="readyToPickUp">Bereit zur Abholung</option>
                    <option value="onDelivery">Wird ausgeliefert</option>
                </select>
                <br>
                <br>
                <input type="submit" name="submitDriverStatus" value="SubmitEchoAbfragen" \>
            </form>
        </div>
    </section>
HTML;
    }

    /**
     * First the necessary data is fetched and then the HTML is 
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if avaialable- the content of 
     * all views contained is generated.
     * Finally the footer is added.
     *
     * @return none
     */
    protected function generateView(){

        $this->getViewData();
        $this->generatePageHeader("Fahrer");
        $this->generatePageBody();
        $this->generatePageFooter();
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
     *
     * @return none 
     */
    protected function processReceivedData(){

        parent::processReceivedData();
    }

    /**
     * Creates an instance of the class and call
     * the methods processReceivedData() and generateView().
     *
     * @return none 
     */
    public static function main(){
        try {
            $page = new Fahrer();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

/**
 * Calling main function to construct and build the page.
 */
Fahrer::main();
