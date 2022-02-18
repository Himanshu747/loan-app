### **Laravel API Demo App**

**Version** 7.0

This app is a demo API app that will give you an idea of the working of APIS in Laravel.

### **Database setup**

Please open your localhost phpmyadmin / adminer, etc.

Navigate to folder SQL Schema File in this project and you will find a file named `laravel-api-demo.sql`. Import that SQL file in you DBs list

### **Request Format and type**

If you are using POSTMAN. Please follow the below process

1. Set request type to `POST`
2. Set your base request URL
3. Select body and then select x-www-form-urlencoded
4. Below are list of APIs with required fields

### **List of APIs**

PN: My local server was http://127.0.0.1:9090 You may change the settings as per you laravel environment

1. http://127.0.0.1:9090/api/register ( Pre Login )
2. http://127.0.0.1:9090/api/login ( Pre Login )
3. http://127.0.0.1:9090/api/applyloan ( Post Login )
4. http://127.0.0.1:9090/api/approveloan ( Post Login )
5. http://127.0.0.1:9090/api/payemi ( Post Login )

### **Application Flow**

PN: You will receive JSON responses to every API request

1. You are a new user so visit `/api/register` to register first. Fields required for this request are `username` & `password` ( Pls note your password will be encrypted so kindly memorize it )

    Expected Response: `{"status":"success","heading":"User created!","message":"Pleae login to continue."}`

2. You need to now login so visit `/api/login`. Fields required for this request are `username` & `password`

    Expect Response: `{"status":"success","heading":"User authenticated!","message":"Welcome to loan application.","data":{"secret":"8761246a220fff678b5bff65c47d6d0c5f0e4e88b4cc9fde550dfa4d5591b1cb"}}`

    PN: secret and username, you will need this for every API request

3. You may apply for a loan by visiting `/api/applyloan` Fields required for this request are `username`, `secret`, `amount` & `tenor`

    Expected Response: `{"status":"success","heading":"Loan applicaton successful!","message":"Your loan has been sent for approval, you will receive a confirmation post approval."}`

4. If you want to approve your loan you may head to `/api/approveloan` Fields required for this request are `username`, `secret`, `type` & `loan_id`

    PN: for type ( You may take a look at `repayment_types` table and search for relevant ID ), for loan_id ( You may take a look at `loans` and search for your relevant loan ID )

    Expected Response: `{"status":"success","heading":"Loan applicaton approved!","message":"User can start paying EMI now. You can set an email ( Using mailgun or any other ) \/ SMS notification ( If DLT Registration done ) to inform the user for further process."}`

5. Now you need to pay EMIs you may visit `/api/payemi` Fields required for this request are `username`, `secret`, `meta_id` & `amount`

    PN: for meta_id ( You may take a look at `payment_metas` and search for relevant meta ID  )

    Expect Response ( For every valid EMI Payment ): `{"status":"success","heading":"EMI Paid successfully!","message":"Next EMI Payment on or before <your_responsed_date>"}`
    Expected Response ( When all the EMIs are completed ): `{"status":"success","heading":"EMIs Completed!","message":"All EMIs were paid were you, no EMIs pending"}`