-- Remove all relationship data before deletion of actual user
CREATE OR ALTER TRIGGER trigger_before_user_delete
    ON [dbo].[users]
    FOR DELETE
    AS
    BEGIN
        DECLARE @id BIGINT;

        SELECT @id = id FROM deleted;

        DELETE FROM user_media WHERE user_id = @id;

        DELETE FROM sessions WHERE user_id = @id;

    END;