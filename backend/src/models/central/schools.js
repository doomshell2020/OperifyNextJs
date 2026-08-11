const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('schools', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    school_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    school_address: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    state: {
      type: DataTypes.STRING(20),
      allowNull: false
    },
    city: {
      type: DataTypes.STRING(200),
      allowNull: false
    },
    school_contact: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    school_database: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "Y"
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    child_domain: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    franchise_type: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    msg_count: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    whatsapp_token: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    instance_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'schools',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
