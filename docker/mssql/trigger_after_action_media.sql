-- Remove all relationship data before deletion of actual user
CREATE OR ALTER TRIGGER trigger_before_media_delete
    ON [dbo].[medias]
    FOR INSERT, UPDATE, DELETE
    AS
    BEGIN
        DECLARE @action char(1);
        SET @action = (CASE
                           WHEN EXISTS(SELECT * FROM inserted)
                               AND EXISTS(SELECT * FROM deleted)
                               THEN 'U' -- Set Action to Updated.
                           WHEN EXISTS(SELECT * FROM inserted)
                               THEN 'I' -- Set Action to Insert.
                           WHEN EXISTS(SELECT * FROM deleted)
                               THEN 'D' -- Set Action to Deleted.
            END)

    END;