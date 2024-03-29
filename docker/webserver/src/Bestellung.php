<?php

require_once './Page.php';

/**
 * Shows the menu with all pizzas and the shopping cart to order.
 * 
 * @author   Julian Segeth
 * @author   Bican Gül 
 */
class Bestellung extends Page {

    /** Contains all pizzas. */
    protected $menu = array();
    
    /**
     * Creates a database connection.
     *
     * @return none
     */
    protected function __construct() {

        parent::__construct();
    }
    
    /**
     * Closes the database connection.
     *
     * @return none
     */
    protected function __destruct() {

        parent::__destruct();
    }

    /**
     * Add additional CSS files and scripts to the head.
     */
    protected function addAdditionalScript() {
echo <<< HTML
    <script src="scripts/cart.js"></script>\n
    <link rel="stylesheet" type="text/css" href="styles/order.css">
HTML;
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is stored in an easily accessible way e.g. as associative array.
     *
     * @return none
     */
    protected function getViewData() {
        
        $this->checkDatabaseConnection();

        // Select data from database.
        $sqlSelect = "SELECT * FROM `menu`";
        $recordSet = $this->connection->query($sqlSelect);

        if ($recordSet->num_rows > 0) {
            while ($row = $recordSet->fetch_assoc()) {

                // Create pizza[] and mask special characters.
                $pizza = array();
                $pizzaName = htmlspecialchars($row["pizzaName"]);
                $pizza["imagePath"] = htmlspecialchars($row["imagePath"]);
                $pizza["pizzaPrice"] = htmlspecialchars($row["pizzaPrice"]);

                // Push pizza[] in menu[].
                $this->menu[$pizzaName] = $pizza;
            }
            $recordSet->free();
            //print_r($this->menu);
        } else {
            echo mysqli_error($this->connection);
        }
    }

    /**
     * Generates the body section of the page.
     * 
     * @return none
     */
    protected function generatePageBody() {
echo <<< HTML
    <div class="orderPage">
    
        <section class="menu">
            <h2>Speisekarte</h2>\n
HTML;    
        foreach ($this->menu as $pizzaName => $pizza) {
            $pizzaPrice = number_format($pizza["pizzaPrice"], 2, ".", ",");
            $imagePath = $pizza["imagePath"];
echo <<< HTML
            <div>
                <img class="img" onclick="addToCart('$pizzaName', $pizzaPrice)" src="$imagePath" alt="$pizzaName" />
                <p>Pizza $pizzaName: $pizzaPrice €</p>
            </div>\n
HTML;
        }
echo <<< HTML
        </section>
        
        <section class="cart">
            <h2>Warenkorb</h2>
            <form action="./Bestellung.php" method="POST">

                <!-- All cart items. -->
                <div class="selectInputs">
                    <div>
                        <p class="cartParagraph">Ihre Auswahl:</p>
                        <select id="cart" name="cart[]" size="4" multiple required>
                        </select>
                    </div>
                    <div>
                        <p id="totalPrice"></p>
                    </div>  
                </div>

                <!-- Text inputs. -->
                <div class="textInputs">
                    <div>
                        <p class="cartParagraph">Ihr Name:</p>
                        <input type="text" name="fullName" required />
                    </div>
                    <div>
                        <p class="cartParagraph">Ihre Adresse:</p>
                        <input type="text" name="address" required />
                    </div>
                </div>

                <!-- Button inputs. -->
                <div class="buttonInputs">
                    <input type="submit" onclick="selectAllOptions()" value="Bestellung aufgeben" />
                    <input type="button" onclick="deleteSelectedOptions()" value="Auswahl entfernen" />
                    <input type="button" onclick="deleteAllOptions()" value="Warenkorb leeren" />
                </div>
            </form>
        </section>
    </div>\n
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
    protected function generateView() {

        $this->getViewData();
        $this->generatePageHeader("Bestellung");
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
    protected function processReceivedData() {

        parent::processReceivedData();

        // Check if POST variables are declared.
        if (isset($_POST["fullName"]) && isset($_POST["address"]) && isset($_POST["cart"])) {

            $this->checkDatabaseConnection();

            // Save POST data into variables and mask special characters.
            $fullName = $this->connection->real_escape_string($_POST["fullName"]);
            $address = $this->connection->real_escape_string($_POST["address"]);
            $cart = $_POST["cart"];

            // Insert order in database.
            $sqlInsert = "INSERT INTO `order` SET `fullName`=\"$fullName\", `address`=\"$address\"";
            $this->connection->query($sqlInsert);

            // Get orderID from the latest insert.
            $orderID = $this->connection->insert_id;

            session_start();
            $_SESSION["orderID"] = "$orderID";
            //print_r($_SESSION);

            // Iterate through cart[] and mask special characters.
            for ($i = 0; $i < count($cart); $i++) {
                $currentPizzaName = $this->connection->real_escape_string($cart[$i]);

                // Insert orderedPizza in database.
                $sqlInsert = "INSERT INTO `orderedPizza` SET `orderID`=$orderID, `pizzaName`=\"$currentPizzaName\", `status`=\"Bestellt\"";
                $this->connection->query($sqlInsert);
            }

            // Redirect on Bestellung.php.
            header('Location: http://localhost/Bestellung.php');
        }
    }

    /**
     * Creates an instance of the class and call
     * the methods processReceivedData() and generateView().
     *
     * @return none 
     */    
    public static function main() {
        try {
            $page = new Bestellung();
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
Bestellung::main();
?>