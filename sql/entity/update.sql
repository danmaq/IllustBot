UPDATE		IB01_ENTITIES
	SET		UPDATED = NOW(),
			BODY = :body
	WHERE	ID = :id;