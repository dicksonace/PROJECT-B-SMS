
# PROJECT B - Messaging Rail (SMPP-style simulator)

## Setup Instructions

### project Setup

3. run this:
   ```bash
   docker compose up --build
   ```

- The backend will be available at `http://localhost:8001`




  

## steps

### 1. Whitelist
- `GET /api/whitelists` - List all Whitelist (paginated)

### 2. Add New Whitelist
- `POST /api/whitelists` - Add New Whitelist

 ```json
{
    "sender_id": "GHSS"
}

``` 

### 3. Send new SMS
- `POST /api/messages` - Create new SMS

    ```text
    Idempotency-Key: send_msg:8484fsfBBBHH
    
   ```
  
  ### Example Request Body

```json

{
    "sender_id":"GHSS",
    "content": "hiccccccFW",
    "to": "344634634"
}

```


### Load Message
- `POST /api/messages/{id}` - Load Message



### Webhook
- `POST /api/webhooks/dir` - Process SMS

-    ### Example Request header

      ```text
     header{
        X-Signature: e40843e55039d3b82678fb231a03ba9fdf3b9cc81347103d153e76797a6246a9,
      }
      
      ```


    ### Example Request Body

```json
{
        "sender_id": "GHSS",
        "receiver": "344634634",
        "content": "hiccccccFW",
        "idempotency_key": "send_msg:8484fsfBBBHH"
    }
```









